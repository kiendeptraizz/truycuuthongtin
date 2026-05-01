<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendingOrder;
use App\Models\Customer;
use App\Models\ServicePackage;
use App\Services\VietQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Quản lý pending orders — đơn được tạo nhanh từ Telegram bot,
 * cuối ngày user vào fill thông tin chi tiết để biến thành CustomerService.
 */
class PendingOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PendingOrder::with(['customerService.customer', 'creator'])
            ->orderByDesc('created_at');

        // Filter status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->paginate(30);

        $stats = [
            'pending' => PendingOrder::where('status', 'pending')->count(),
            'today' => PendingOrder::whereDate('created_at', today())->count(),
            'today_amount' => PendingOrder::whereDate('created_at', today())->sum('amount'),
        ];

        return view('admin.pending-orders.index', compact('orders', 'stats', 'status'));
    }

    public function store(Request $request)
    {
        // Hỗ trợ cả 2 định dạng: amount (số nguyên từ JS hidden field) hoặc amount_input ("100k", "1.5tr")
        $rawAmount = $request->input('amount');
        if (!$rawAmount && $request->filled('amount_input')) {
            $rawAmount = parseShortAmount($request->input('amount_input'));
            $request->merge(['amount' => $rawAmount]);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $order = $this->createOrder([
            'amount' => parseShortAmount($validated['amount']),
            'note' => $validated['note'] ?? null,
            'created_via' => 'web',
            'created_by' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã tạo đơn {$order->order_code}",
                'order' => $order,
                'qr_url' => $order->qrCodeUrl(),
            ]);
        }

        return redirect()->route('admin.pending-orders.index')
            ->with('success', "Đã tạo đơn {$order->order_code}");
    }

    /**
     * Logic tạo đơn — dùng chung cho web + Telegram bot.
     */
    public static function createOrder(array $data): PendingOrder
    {
        return DB::transaction(function () use ($data) {
            // Retry tối đa 5 lần nếu order_code trùng (race-safe)
            $attempts = 0;
            while ($attempts < 5) {
                try {
                    return PendingOrder::create(array_merge($data, [
                        'order_code' => PendingOrder::generateOrderCode(),
                        'status' => 'pending',
                    ]));
                } catch (\Illuminate\Database\QueryException $e) {
                    if (str_contains($e->getMessage(), 'order_code')) {
                        $attempts++;
                        usleep(50000); // 50ms
                        continue;
                    }
                    throw $e;
                }
            }
            throw new \RuntimeException('Không tạo được mã đơn duy nhất sau 5 lần thử.');
        });
    }

    /**
     * Form fill thông tin chi tiết → tạo CustomerService.
     */
    public function fillForm(PendingOrder $pendingOrder)
    {
        if ($pendingOrder->status !== 'pending') {
            return redirect()->route('admin.pending-orders.index')
                ->with('warning', "Đơn {$pendingOrder->order_code} đã được xử lý.");
        }

        $customers = Customer::orderBy('name')->limit(500)->get();
        $servicePackages = ServicePackage::with('category')->where('is_active', true)
            ->orderBy('name')->get();

        return view('admin.pending-orders.fill', compact('pendingOrder', 'customers', 'servicePackages'));
    }

    /**
     * Submit form fill → tạo CustomerService + đánh dấu pending order là completed.
     */
    public function fill(Request $request, PendingOrder $pendingOrder)
    {
        if ($pendingOrder->status !== 'pending') {
            return back()->withErrors(['error' => 'Đơn này đã được xử lý.']);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_package_id' => 'required|exists:service_packages,id',
            'login_email' => 'required|email|max:255',
            'login_password' => 'nullable|string|max:255',
            'activated_at' => 'required|date',
            'expires_at' => 'required|date|after:activated_at',
            'duration_days' => 'required|integer|min:1',
            'profit_amount' => 'nullable|numeric|min:0',
            'profit_notes' => 'nullable|string|max:1000',
            'internal_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request, $pendingOrder) {
            // Tạo CustomerService
            $cs = \App\Models\CustomerService::create([
                'customer_id' => $validated['customer_id'],
                'service_package_id' => $validated['service_package_id'],
                'assigned_by' => auth()->id(),
                'login_email' => $validated['login_email'],
                'login_password' => $validated['login_password'] ?? null,
                'activated_at' => $validated['activated_at'],
                'expires_at' => $validated['expires_at'],
                'status' => 'active',
                'duration_days' => $validated['duration_days'],
                'price' => 0, // theo cấu hình project: chỉ tính profit
                'cost_price' => 0,
                'internal_notes' => ($validated['internal_notes'] ?? '') . "\n\n📋 Tạo từ pending order {$pendingOrder->order_code} ({$pendingOrder->amount}đ)",
            ]);

            // Tạo Profit nếu có
            if (!empty($validated['profit_amount']) && $validated['profit_amount'] > 0) {
                \App\Models\Profit::create([
                    'customer_service_id' => $cs->id,
                    'profit_amount' => $validated['profit_amount'],
                    'notes' => $validated['profit_notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);
            }

            // Đánh dấu pending order completed + link với customer service
            $pendingOrder->update([
                'status' => 'completed',
                'customer_service_id' => $cs->id,
            ]);

            Log::info('Pending order filled', [
                'order_code' => $pendingOrder->order_code,
                'customer_service_id' => $cs->id,
                'by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.pending-orders.index')
            ->with('success', "Đã fill đơn {$pendingOrder->order_code} thành công.");
    }

    public function destroy(Request $request, PendingOrder $pendingOrder)
    {
        if ($pendingOrder->status === 'completed') {
            $msg = 'Không thể xoá đơn đã hoàn thành. Hãy xoá CustomerService liên quan trước.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['error' => $msg]);
        }

        $code = $pendingOrder->order_code;
        $pendingOrder->update(['status' => 'cancelled']);

        Log::info('Pending order cancelled', ['code' => $code, 'by' => auth()->id()]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã huỷ đơn {$code}",
                'id' => $pendingOrder->id,
            ]);
        }

        return back()->with('success', "Đã huỷ đơn {$code}");
    }

    /**
     * Hiển thị QR thanh toán riêng (modal trên web).
     */
    public function qr(PendingOrder $pendingOrder)
    {
        return view('admin.pending-orders.qr-modal', [
            'order' => $pendingOrder,
            'qrUrl' => $pendingOrder->qrCodeUrl(),
            'qr' => app(VietQrService::class),
        ]);
    }
}
