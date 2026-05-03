<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\HomeSettings;
use App\Models\PendingOrder;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;

class LookupController extends Controller
{
    public function index(Request $request)
    {
        $customer = null;
        $services = collect();
        $singleService = null; // Khi tra cứu theo mã đơn → chỉ 1 dịch vụ
        $groupCode = null;     // Khi tra cứu theo mã lô (GR-XXX) → N dịch vụ cùng lô
        $code = $request->get('code');

        // Stats + categories cho trang chủ — cache 1 giờ vì ít đổi.
        // Admin có thể override 3 số ở /admin/home-settings để boost FOMO/uy tín.
        $stats = Cache::remember('home_stats', 3600, function () {
            $settings = HomeSettings::singleton();
            $real = [
                'customers' => Customer::count(),
                'services' => CustomerService::where('status', 'active')->count(),
                'packages' => ServicePackage::where('is_active', true)->count(),
            ];
            return [
                'customers' => $settings->customers_override ?? $real['customers'],
                'services' => $settings->services_override ?? $real['services'],
                'packages' => $settings->packages_override ?? $real['packages'],
            ];
        });

        $categories = Cache::remember('home_categories', 3600, function () {
            return ServiceCategory::query()
                ->withCount(['servicePackages as active_count' => fn($q) => $q->where('is_active', true)])
                ->having('active_count', '>', 0)
                ->orderBy('name')
                ->get();
        });

        if ($code) {
            $code = trim($code);

            // 1) Thử match mã LÔ GR-YYMMDD-XXX trước (lô đơn nhiều dịch vụ)
            $groupResult = $this->findServicesByGroupCode($code);
            if ($groupResult) {
                $customer = $groupResult['customer'];
                $services = $groupResult['services'];
                $groupCode = $groupResult['group_code'];
            } else {
                // 2) Thử match mã đơn DH-YYMMDD-XXX (cả format có/không dấu gạch)
                $singleService = $this->findServiceByOrderCode($code);
                if ($singleService) {
                    $customer = $singleService->customer;
                    $services = collect([$singleService]);
                } else {
                    // 3) Tìm theo mã KH / email / phone, fallback tên Zalo
                    $customer = $this->findCustomer($code);

                    if ($customer) {
                        $services = $customer->customerServices()
                            ->with('servicePackage.category')
                            ->orderBy('created_at', 'desc')
                            ->get();
                    }
                }
            }
        }

        return view('lookup.index', compact('customer', 'services', 'singleService', 'groupCode', 'code', 'stats', 'categories'));
    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:255'
            ]);

            $code = trim($request->input('code'));
            $groupCode = null;

            // 1) Thử match mã LÔ GR-XXX trước (lô nhiều đơn)
            $groupResult = $this->findServicesByGroupCode($code);
            if ($groupResult) {
                $customer = $groupResult['customer'];
                $services = $groupResult['services'];
                $groupCode = $groupResult['group_code'];
            } else {
                // 2) Thử match mã đơn DH-XXX
                $singleService = $this->findServiceByOrderCode($code);
                if ($singleService && $singleService->customer) {
                    $customer = $singleService->customer;
                    $services = collect([$singleService]);
                } else {
                    // 3) Tìm theo mã KH / email / phone, fallback tên Zalo
                    $customer = $this->findCustomer($code);

                    if (!$customer) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Không tìm thấy thông tin. Vui lòng kiểm tra lại mã đơn (DH-XXX), mã lô (GR-XXX), mã khách hàng (KUN/CTV), tên Zalo, email hoặc số điện thoại.'
                        ], 404);
                    }

                    $services = $customer->customerServices()
                        ->with('servicePackage.category')
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'customer_code' => $customer->customer_code,
                        'created_at' => $customer->created_at->format('d/m/Y'),
                    ],
                    'group_code' => $groupCode, // null nếu không phải search by lô
                    'services' => $services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'order_code' => $service->order_code,
                            'package_name' => $service->servicePackage->name ?? 'N/A',
                            'category_name' => $service->servicePackage->category->name ?? 'N/A',
                            'status' => $service->status,
                            'price' => $service->servicePackage->price ?? 0,
                            'activated_at' => $service->activated_at ? $service->activated_at->toISOString() : null,
                            'expires_at' => $service->expires_at ? $service->expires_at->toISOString() : null,
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Tìm tất cả CustomerService thuộc 1 mã LÔ GR-yymmdd-XXX. Chấp nhận format
     * có/không dấu gạch (vd "GR-260503-001" hoặc "GR260503001").
     *
     * Logic: tìm tất cả PendingOrder có group_code → lấy các customer_service_id
     * đã link → query CustomerService. Trả null nếu không match.
     *
     * @return array{customer: Customer, services: \Illuminate\Support\Collection, group_code: string}|null
     */
    private function findServicesByGroupCode(string $code): ?array
    {
        $upper = strtoupper(trim($code));
        if (!preg_match('/^GR[-\s]?\d/i', $upper)) {
            return null;
        }
        $stripped = str_replace(['-', ' '], '', $upper);

        $orders = PendingOrder::where('group_code', $upper)
            ->orWhereRaw('UPPER(REPLACE(group_code, "-", "")) = ?', [$stripped])
            ->with('customer', 'customerService.servicePackage.category')
            ->orderBy('order_code')
            ->get();

        if ($orders->isEmpty()) {
            return null;
        }

        $customer = $orders->first()->customer;
        if (!$customer) {
            return null;
        }

        // Lấy CS đã link với từng PO trong lô (chỉ lấy CS đã active sau khi paid)
        $services = $orders->pluck('customerService')->filter()->values();

        // Group code chuẩn (sau khi normalize) để view banner show
        $groupCode = $orders->first()->group_code;

        return [
            'customer' => $customer,
            'services' => $services,
            'group_code' => $groupCode,
        ];
    }

    /**
     * Tìm khách hàng theo mã KH / email / phone (exact), fallback theo tên Zalo
     * (LIKE %code%, sắp xếp theo id ASC để lấy KH cũ nhất nếu trùng tên).
     * Yêu cầu len(code) >= 2 mới fallback name để tránh match toàn bộ.
     */
    private function findCustomer(string $code): ?Customer
    {
        $customer = Customer::where(function ($query) use ($code) {
            $query->where('customer_code', $code)
                ->orWhere('email', $code)
                ->orWhere('phone', $code);
        })
            ->with(['customerServices.servicePackage.category'])
            ->first();

        if (!$customer && mb_strlen($code) >= 2) {
            // Fuzzy search by name — bỏ dấu tiếng Việt + lowercase 2 phía
            // để khách gõ "phung dai" vẫn match "Phùng Văn Đại".
            $stripped = $this->stripVietnameseAccents($code);
            $stripped = mb_strtolower($stripped);

            // Pass 1: LIKE thẳng (có dấu) — nhanh nhất
            $customer = Customer::where('name', 'LIKE', '%' . $code . '%')
                ->with(['customerServices.servicePackage.category'])
                ->orderBy('id')
                ->first();

            // Pass 2: nếu không match, scan customers có tên chứa keyword sau khi strip
            // (chỉ chạy khi pass 1 fail — giảm load query). Limit 200 KH gần nhất.
            if (!$customer) {
                $candidates = Customer::orderByDesc('id')->limit(200)->get(['id', 'name']);
                $matchedId = null;
                foreach ($candidates as $cand) {
                    $candStripped = mb_strtolower($this->stripVietnameseAccents($cand->name));
                    if (str_contains($candStripped, $stripped)) {
                        $matchedId = $cand->id;
                        break;
                    }
                }
                if ($matchedId) {
                    $customer = Customer::with(['customerServices.servicePackage.category'])
                        ->find($matchedId);
                }
            }
        }

        return $customer;
    }

    /**
     * Bỏ dấu tiếng Việt: "Phùng Văn Đại" → "Phung Van Dai".
     * Dùng cho fuzzy match — không thay đổi data gốc.
     */
    private function stripVietnameseAccents(string $str): string
    {
        $vi = [
            'à','á','ả','ã','ạ','ă','ằ','ắ','ẳ','ẵ','ặ','â','ầ','ấ','ẩ','ẫ','ậ',
            'è','é','ẻ','ẽ','ẹ','ê','ề','ế','ể','ễ','ệ',
            'ì','í','ỉ','ĩ','ị',
            'ò','ó','ỏ','õ','ọ','ô','ồ','ố','ổ','ỗ','ộ','ơ','ờ','ớ','ở','ỡ','ợ',
            'ù','ú','ủ','ũ','ụ','ư','ừ','ứ','ử','ữ','ự',
            'ỳ','ý','ỷ','ỹ','ỵ',
            'đ',
            'À','Á','Ả','Ã','Ạ','Ă','Ằ','Ắ','Ẳ','Ẵ','Ặ','Â','Ầ','Ấ','Ẩ','Ẫ','Ậ',
            'È','É','Ẻ','Ẽ','Ẹ','Ê','Ề','Ế','Ể','Ễ','Ệ',
            'Ì','Í','Ỉ','Ĩ','Ị',
            'Ò','Ó','Ỏ','Õ','Ọ','Ô','Ồ','Ố','Ổ','Ỗ','Ộ','Ơ','Ờ','Ớ','Ở','Ỡ','Ợ',
            'Ù','Ú','Ủ','Ũ','Ụ','Ư','Ừ','Ứ','Ử','Ữ','Ự',
            'Ỳ','Ý','Ỷ','Ỹ','Ỵ',
            'Đ',
        ];
        $en = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd',
            'A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
            'E','E','E','E','E','E','E','E','E','E','E',
            'I','I','I','I','I',
            'O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
            'U','U','U','U','U','U','U','U','U','U','U',
            'Y','Y','Y','Y','Y',
            'D',
        ];
        return str_replace($vi, $en, $str);
    }

    /**
     * Tìm CustomerService theo mã đơn DH-YYMMDD-XXX. Nhận cả format có/không
     * dấu gạch (vd "DH-260502-001" hoặc "DH260502001"). Returns null nếu
     * không match.
     */
    private function findServiceByOrderCode(string $code): ?CustomerService
    {
        $upper = strtoupper(trim($code));
        // Phải bắt đầu DH + chữ số (cho phép dấu gạch hoặc khoảng trắng giữa)
        if (!preg_match('/^DH[-\s]?\d/i', $upper)) {
            return null;
        }
        $stripped = str_replace(['-', ' '], '', $upper);

        return CustomerService::where('order_code', $upper)
            ->orWhereRaw('UPPER(REPLACE(order_code, "-", "")) = ?', [$stripped])
            ->with('customer', 'servicePackage.category')
            ->first();
    }
}
