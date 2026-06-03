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

        // Bỏ limit 500 — load all customers vì hidden select của component cần đủ option
        // để JS selectCustomer() set value match. Nếu thiếu option, submit customer_id rỗng.
        // 896 customers (snapshot) → page load vẫn nhẹ. Nếu sau này >10k cần AJAX search.
        $customers = Customer::orderBy('name')->get();
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
        // URL fragment #order-{id} → browser tự scroll đến row vừa update sau reload,
        // không bị nhảy về đầu trang (kết hợp với admin-scroll-preserve.js để robust).
        return back()->with('success', $msg)->withFragment("order-{$pendingOrder->id}");
    }

    /**
     * Đánh dấu đơn HOÀN THÀNH thủ công — dành cho đơn nhanh CTV không cần fill chi tiết.
     *
     * Khác markPaid (yêu cầu Pay2S match hoặc admin xác nhận khách CK):
     *   - markCompleted dùng cho đơn user chủ động đánh dấu xong (vd CTV đã thanh toán
     *     ngoài hệ thống, hoặc đơn nội bộ không cần fill gói/email).
     *
     * Behavior:
     *   - status='cancelled' → reject
     *   - status='completed' → idempotent return success
     *   - status='pending':
     *     * Chưa paid → set paid_at=now() + paid_amount=amount + status='completed'
     *     * Đã paid → chỉ set status='completed'
     *   - TẠO CS PLACEHOLDER (sau feedback user 4/6/2026: muốn đơn hoàn thành cũng
     *     xuất hiện ở /admin/customer-services) với data tối thiểu:
     *     * customer_id, service_package_id, login_email có thể NULL (migration
     *       2026_06_04_014500 đã cho phép)
     *     * order_code, order_amount, duration_days lấy từ PO
     *     * activated_at = paid_at, expires_at = activated_at + duration_days (nếu có)
     *     * status='active' (đơn đã hoàn thành tức là đang chạy)
     *     * internal_notes đánh dấu rõ đây là CS từ markCompleted để view phân biệt
     *   - TẠO Profit nếu PO.profit_amount > 0 (Profit FK customer_service_id)
     */
    public function markCompleted(Request $request, PendingOrder $pendingOrder)
    {
        if ($pendingOrder->status === 'cancelled') {
            $msg = 'Không thể đánh dấu đơn đã huỷ là hoàn thành.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['error' => $msg]);
        }

        // Idempotent: chỉ early-return nếu đã completed VÀ CS đã tồn tại.
        // Đơn completed chưa có CS (vd marked qua commit cũ trước khi có logic tạo CS
        // placeholder) → vẫn cho click để backfill CS.
        if ($pendingOrder->status === 'completed' && $pendingOrder->customer_service_id) {
            $msg = "Đơn {$pendingOrder->order_code} đã được đánh dấu hoàn thành trước đó.";
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('info', $msg)->withFragment("order-{$pendingOrder->id}");
        }

        $csId = null;
        \DB::transaction(function () use ($pendingOrder, &$csId) {
            $locked = PendingOrder::where('id', $pendingOrder->id)->lockForUpdate()->first();
            if (!$locked) {
                return;
            }
            // Cho phép cả pending lẫn completed-chưa-CS (backfill mode)
            if ($locked->status !== 'pending' && !($locked->status === 'completed' && !$locked->customer_service_id)) {
                return;
            }

            $update = ['status' => 'completed'];
            // Nếu chưa paid → set luôn paid_at + paid_amount để stat ghi nhận doanh thu.
            if (!$locked->paid_at) {
                $update['paid_at'] = now();
                $update['paid_amount'] = (int) $locked->amount;
                $update['bank_transaction_id'] = 'manual-complete-' . auth()->id() . '-' . now()->timestamp;
                $update['bank_raw_payload'] = json_encode([
                    'source' => 'manual_complete',
                    'admin_id' => auth()->id(),
                    'admin_name' => auth()->user()?->name,
                    'marked_at' => now()->toIso8601String(),
                    'note' => 'Đánh dấu hoàn thành thủ công (đơn CTV không fill chi tiết).',
                ], JSON_UNESCAPED_UNICODE);
            }
            $locked->update($update);
            $locked->refresh();

            // Tạo CS placeholder nếu chưa có (đơn bot full 7-step đã có CS pending →
            // chỉ activate nếu cần). Idempotent: nếu đã có CS link → skip create.
            if ($locked->customer_service_id) {
                // Đơn full 7-step → CS pending có sẵn → activate giống PaymentService
                $cs = \App\Models\CustomerService::find($locked->customer_service_id);
                if ($cs && $cs->status === 'pending') {
                    $activatedAt = $locked->paid_at ?? now();
                    $cs->update([
                        'status' => 'active',
                        'activated_at' => $activatedAt,
                        'expires_at' => $cs->duration_days ? $activatedAt->copy()->addDays((int) $cs->duration_days) : null,
                        'internal_notes' => trim(($cs->internal_notes ?? '')
                            . "\n\n🏃 Đánh dấu hoàn thành thủ công qua web ({$locked->paid_at->format('d/m/Y H:i')})"),
                    ]);
                    $csId = $cs->id;
                }
            } else {
                // Đơn nhanh → tạo CS placeholder mới với data tối thiểu từ PO
                $activatedAt = $locked->paid_at ?? now();
                $expiresAt = $locked->duration_days
                    ? $activatedAt->copy()->addDays((int) $locked->duration_days)
                    : null;

                $cs = \App\Models\CustomerService::create([
                    'pending_order_id' => $locked->id,
                    'order_code' => $locked->order_code,
                    'customer_id' => $locked->customer_id, // NULL OK (bot user có thể bỏ qua KH)
                    'service_package_id' => null, // placeholder — đơn nhanh không có gói
                    'login_email' => null,
                    'order_amount' => $locked->amount,
                    'duration_days' => $locked->duration_days,
                    'activated_at' => $activatedAt,
                    'expires_at' => $expiresAt,
                    'status' => 'active',
                    'price' => 0,
                    'cost_price' => 0,
                    'internal_notes' => "🏃 Đơn nhanh CTV — đánh dấu hoàn thành thủ công qua web ("
                        . now()->format('d/m/Y H:i') . ") bởi " . (auth()->user()?->name ?? 'admin')
                        . ". KHÔNG có gói/email cụ thể — đây là đơn nhanh không cần fill chi tiết.",
                ]);
                $locked->update(['customer_service_id' => $cs->id]);
                $csId = $cs->id;

                // Tạo Profit từ PO.profit_amount nếu có
                if (!empty($locked->profit_amount) && (int) $locked->profit_amount > 0) {
                    \App\Models\Profit::create([
                        'customer_service_id' => $cs->id,
                        'profit_amount' => $locked->profit_amount,
                        'notes' => "Tự tạo từ đơn nhanh {$locked->order_code} qua nút Hoàn thành",
                        'created_by' => auth()->id(),
                    ]);
                }
            }
        });

        Log::info('Manual markCompleted success', [
            'order_id' => $pendingOrder->id,
            'order_code' => $pendingOrder->order_code,
            'admin_id' => auth()->id(),
            'was_paid_before' => $pendingOrder->paid_at !== null,
            'cs_id_created' => $csId,
        ]);

        $msg = "✅ Đã đánh dấu đơn {$pendingOrder->order_code} là hoàn thành"
            . ($csId ? " và tạo dịch vụ #{$csId} (đơn nhanh CTV)." : '.')
            . " Đơn này sẽ tính vào doanh thu/lợi nhuận và xuất hiện trong Dịch vụ khách hàng.";
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'cs_id' => $csId]);
        }
        return back()->with('success', $msg)->withFragment("order-{$pendingOrder->id}");
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
