<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;

class LookupController extends Controller
{
    public function index(Request $request)
    {
        $customer = null;
        $services = collect();
        $singleService = null; // Khi tra cứu theo mã đơn → chỉ 1 dịch vụ
        $code = $request->get('code');

        // Stats + categories cho trang chủ — cache 1 giờ vì ít đổi
        $stats = Cache::remember('home_stats', 3600, function () {
            return [
                'customers' => Customer::count(),
                'services' => CustomerService::where('status', 'active')->count(),
                'packages' => ServicePackage::where('is_active', true)->count(),
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

            // 1) Thử match mã đơn DH-YYMMDD-XXX trước (cả format có/không dấu gạch)
            $singleService = $this->findServiceByOrderCode($code);
            if ($singleService) {
                $customer = $singleService->customer;
                $services = collect([$singleService]);
            } else {
                // 2) Tìm theo mã khách hàng / email / phone
                $customer = Customer::where(function ($query) use ($code) {
                    $query->where('customer_code', $code)
                        ->orWhere('email', $code)
                        ->orWhere('phone', $code);
                })
                    ->with(['customerServices.servicePackage.category'])
                    ->first();

                if ($customer) {
                    $services = $customer->customerServices()
                        ->with('servicePackage.category')
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            }
        }

        return view('lookup.index', compact('customer', 'services', 'singleService', 'code', 'stats', 'categories'));
    }

    public function search(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:255'
            ]);

            $code = trim($request->input('code'));

            // 1) Thử match mã đơn trước
            $singleService = $this->findServiceByOrderCode($code);
            if ($singleService && $singleService->customer) {
                $customer = $singleService->customer;
                $services = collect([$singleService]);
            } else {
                // 2) Tìm theo mã khách hàng / email / phone (exact match)
                $customer = Customer::where(function ($query) use ($code) {
                    $query->where('customer_code', $code)
                        ->orWhere('email', $code)
                        ->orWhere('phone', $code);
                })
                    ->with(['customerServices.servicePackage.category'])
                    ->first();

                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy thông tin. Vui lòng kiểm tra lại mã đơn (DH-XXX), mã khách hàng (KUN/CTV), email hoặc số điện thoại.'
                    ], 404);
                }

                $services = $customer->customerServices()
                    ->with('servicePackage.category')
                    ->orderBy('created_at', 'desc')
                    ->get();
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
