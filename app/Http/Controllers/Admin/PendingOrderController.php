<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendingOrder;
use App\Models\Customer;
use App\Models\ServicePackage;
use App\Services\PaymentService;
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
        $query = PendingOrder::with(['customer', 'customerService.customer', 'creator'])
            ->orderByDesc('created_at');

        // Filter status — "all" mặc định EXCLUDE cancelled (đơn huỷ ít cần action,
        // chỉ hiện khi user chọn explicit "Đã huỷ" hoặc xem riêng).
        $status = $request->get('status', 'pending');
        if ($status === 'all') {
            $query->where('status', '!=', 'cancelled');
        } else {
            $query->where('status', $status);
        }

        // Filter date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter theo mã/tên khách hàng — match qua direct customer_id hoặc CS link.
        // Hữu ích để biết 1 KH còn bao nhiêu đơn pending.
        $customerSearch = trim((string) $request->get('customer', ''));
        if ($customerSearch !== '') {
            $query->where(function ($q) use ($customerSearch) {
                // Match qua direct customer_id (đơn nhanh / đơn đầy đủ qua bot)
                $q->whereHas('customer', function ($cq) use ($customerSearch) {
                    $cq->where('customer_code', 'LIKE', "%{$customerSearch}%")
                        ->orWhere('name', 'LIKE', "%{$customerSearch}%");
                })
                // Match qua customerService.customer (đơn cũ link qua CS)
                ->orWhereHas('customerService.customer', function ($cq) use ($customerSearch) {
                    $cq->where('customer_code', 'LIKE', "%{$customerSearch}%")
                        ->orWhere('name', 'LIKE', "%{$customerSearch}%");
                });
            });
        }

        // Filter theo trạng thái thanh toán (paid / unpaid / all)
        $paidFilter = $request->get('paid', 'all');
        if ($paidFilter === 'paid') {
            $query->whereNotNull('paid_at');
        } elseif ($paidFilter === 'unpaid') {
            $query->whereNull('paid_at');
        }

        // Filter theo nguồn (telegram / web / all)
        $source = $request->get('source', 'all');
        if (in_array($source, ['telegram', 'web'], true)) {
            $query->where('created_via', $source);
        }

        // Search theo mã đơn (DH-yymmdd-XXX)
        $code = trim((string) $request->get('code', ''));
        if ($code !== '') {
            $query->where('order_code', 'LIKE', "%" . strtoupper($code) . "%");
        }

        $orders = $query->paginate(30)->withQueryString();

        $stats = [
            'pending' => PendingOrder::where('status', 'pending')->count(),
            // Đơn paid nhưng chưa fill xong (CS chưa active) — cần xử lý gấp
            'needs_fill_urgent' => PendingOrder::where('status', 'pending')
                ->whereNotNull('paid_at')
                ->whereNull('customer_service_id')
                ->count(),
            'today' => PendingOrder::whereDate('created_at', today())->count(),
            'today_amount' => PendingOrder::whereDate('created_at', today())->sum('amount'),
        ];

        return view('admin.pending-orders.index', compact('orders', 'stats', 'status', 'customerSearch', 'paidFilter', 'source', 'code'));
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

        // Eager load customer + servicePackage để view show "Đã pre-fill từ bot: KUN-XXX — Tên"
        $pendingOrder->load(['customer', 'servicePackage']);

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
            'family_code' => 'nullable|string|max:255',
            'warranty_days' => 'nullable|integer|min:0',
            // Money inputs là string (có thể chứa dấu chấm phân cách "100.000" hoặc shortcut "100k") — parse qua parseShortAmount() bên dưới
            'order_amount' => 'nullable|string|max:30',
            'profit_amount' => 'nullable|string|max:30',
            'profit_notes' => 'nullable|string|max:1000',
            'internal_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request, $pendingOrder) {
            $pendingOrder->refresh();

            // Nếu đơn này đã có CS pending (do bot Telegram tạo qua hybrid flow)
            // → UPDATE thay vì create mới (tránh duplicate order_code UNIQUE)
            $existingCs = $pendingOrder->customer_service_id
                ? \App\Models\CustomerService::find($pendingOrder->customer_service_id)
                : null;

            // order_amount lấy từ form (admin có thể sửa). Parse qua parseShortAmount()
            // để hỗ trợ format "100.000" (dấu chấm phân cách) và shortcut "100k"/"1.5tr".
            $orderAmount = !empty($validated['order_amount'])
                ? (float) parseShortAmount($validated['order_amount'])
                : (float) $pendingOrder->amount;

            // profit_amount: parse tương tự
            $profitAmount = !empty($validated['profit_amount'])
                ? (float) parseShortAmount($validated['profit_amount'])
                : 0;

            $payload = [
                'customer_id' => $validated['customer_id'],
                'service_package_id' => $validated['service_package_id'],
                'assigned_by' => auth()->id(),
                'login_email' => $validated['login_email'],
                'activated_at' => $validated['activated_at'],
                'expires_at' => $validated['expires_at'],
                'status' => 'active',
                'duration_days' => $validated['duration_days'],
                'family_code' => $validated['family_code'] ?? null,
                'warranty_days' => $validated['warranty_days'] ?? null,
                'order_amount' => $orderAmount,
            ];

            if ($existingCs) {
                // ACTIVATE CS pending — chỉ override login_password nếu user nhập mới
                if (!empty($validated['login_password'])) {
                    $payload['login_password'] = $validated['login_password'];
                }
                $payload['internal_notes'] = trim(
                    ($existingCs->internal_notes ?? '')
                    . "\n\n📋 Fill thủ công qua web (" . now()->format('d/m/Y H:i') . ")"
                    . (!empty($validated['internal_notes']) ? "\n" . $validated['internal_notes'] : '')
                );
                $existingCs->update($payload);
                $cs = $existingCs;
            } else {
                // Chưa có CS — tạo mới
                $cs = \App\Models\CustomerService::create($payload + [
                    'login_password' => $validated['login_password'] ?? null,
                    'pending_order_id' => $pendingOrder->id,
                    'price' => 0, // theo cấu hình project: chỉ tính profit
                    'cost_price' => 0,
                    'internal_notes' => ($validated['internal_notes'] ?? '') . "\n\n📋 Tạo từ pending order {$pendingOrder->order_code} ({$pendingOrder->amount}đ)",
                ]);
                $pendingOrder->update(['customer_service_id' => $cs->id]);
            }

            // Profit — update nếu có, tạo mới nếu chưa (dùng giá đã parse từ shortcut)
            if ($profitAmount > 0) {
                if ($cs->profit) {
                    $cs->profit->update([
                        'profit_amount' => $profitAmount,
                        'notes' => $validated['profit_notes'] ?? null,
                    ]);
                } else {
                    \App\Models\Profit::create([
                        'customer_service_id' => $cs->id,
                        'profit_amount' => $profitAmount,
                        'notes' => $validated['profit_notes'] ?? null,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            // Đánh dấu pending order completed + đồng bộ amount nếu admin sửa
            $orderUpdate = ['status' => 'completed'];
            if ((float) $orderAmount !== (float) $pendingOrder->amount) {
                $orderUpdate['amount'] = $orderAmount;
            }
            $pendingOrder->update($orderUpdate);

            Log::info('Pending order filled', [
                'order_code' => $pendingOrder->order_code,
                'customer_service_id' => $cs->id,
                'mode' => $existingCs ? 'activated_existing' : 'created_new',
                'order_amount' => $orderAmount,
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
     * Manual mark đơn pending là đã thanh toán (admin xác nhận thủ công khi
     * Pay2S webhook fail / khách CK bằng cách khác / test).
     *
     * Dùng cùng PaymentService như Pay2S webhook → atomic + activate/create CS
     * + tạo Profit nhất quán.
     */
    public function markPaid(Request $request, PendingOrder $pendingOrder, PaymentService $payment)
    {
        if ($pendingOrder->status === 'cancelled') {
            $msg = 'Không thể đánh dấu đơn đã huỷ là đã thanh toán.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['error' => $msg]);
        }

        $validated = $request->validate([
            'paid_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        // Default = số tiền đơn nếu admin không sửa
        $amount = (int) ($validated['paid_amount'] ?? $pendingOrder->amount);
        $bankTxId = 'manual-' . auth()->id() . '-' . now()->timestamp;
        $rawPayload = json_encode([
            'source' => 'manual',
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()?->name,
            'note' => $validated['note'] ?? null,
            'marked_at' => now()->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE);

        $result = $payment->markOrderPaid($pendingOrder, $amount, $bankTxId, $rawPayload, 'manual');

        if (!$result['ok']) {
            $msg = 'Lỗi đánh dấu thanh toán: ' . ($result['error'] ?? $result['status']);
            Log::error('Manual markPaid failed', [
                'order_id' => $pendingOrder->id,
                'admin_id' => auth()->id(),
                'result' => $result,
            ]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }
            return back()->withErrors(['error' => $msg]);
        }

        if ($result['status'] === 'already_paid') {
            $msg = "Đơn {$pendingOrder->order_code} đã được đánh dấu thanh toán trước đó.";
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $msg, 'cs_id' => $result['cs_id'] ?? null]);
            }
            return back()->with('info', $msg);
        }

        $csId = $result['cs_id'] ?? null;
        $msg = "✅ Đã đánh dấu đơn {$pendingOrder->order_code} là thanh toán" .
            ($csId ? " và tạo dịch vụ #{$csId} cho khách." : '. Cần fill thủ công vì thiếu data.');

        Log::info('Manual markPaid success', [
            'order_id' => $pendingOrder->id,
            'order_code' => $pendingOrder->order_code,
            'admin_id' => auth()->id(),
            'amount' => $amount,
            'cs_id' => $csId,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'cs_id' => $csId,
            ]);
        }
        return back()->with('success', $msg);
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
