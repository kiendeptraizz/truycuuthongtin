<?php

namespace App\Services;

use App\Models\CustomerService;
use App\Models\PendingOrder;
use App\Models\Profit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Logic mark đơn paid + activate/create CustomerService — chia sẻ giữa
 * Pay2S webhook (auto từ bank) và admin UI (manual mark paid khi Pay2S fail).
 *
 * Mọi flow đi qua đây để có cùng:
 *   - Atomic DB transaction + lockForUpdate trên PendingOrder
 *   - Idempotent layer 2 ($order->paid_at !== null trong lock)
 *   - Cùng logic activate pending CS hoặc create active CS mới
 *   - Cùng format Profit auto-create
 */
class PaymentService
{
    /**
     * Mark 1 PendingOrder là đã thanh toán + activate/create CustomerService.
     *
     * @param  PendingOrder $order Đơn cần mark (chưa cần lock — service tự lock).
     * @param  int          $amount  Số tiền nhận (đồng).
     * @param  string|null  $bankTxId  ID giao dịch ngân hàng / null nếu manual.
     * @param  string|null  $rawPayload  Raw payload (JSON từ Pay2S, hoặc null/manual).
     * @param  string       $source  'pay2s' | 'manual' | tên khác để log.
     * @return array{ok: bool, status: string, cs_id?: int|null, error?: string}
     */
    public function markOrderPaid(
        PendingOrder $order,
        int $amount,
        ?string $bankTxId = null,
        ?string $rawPayload = null,
        string $source = 'pay2s'
    ): array {
        try {
            return DB::transaction(function () use ($order, $amount, $bankTxId, $rawPayload, $source) {
                // Lock + reload để đảm bảo state mới nhất
                $locked = PendingOrder::where('id', $order->id)->lockForUpdate()->first();
                if (!$locked) {
                    return ['ok' => false, 'status' => 'not_found'];
                }
                if ($locked->paid_at !== null) {
                    return ['ok' => true, 'status' => 'already_paid', 'cs_id' => $locked->customer_service_id];
                }

                $locked->update([
                    'paid_at' => now(),
                    'paid_amount' => $amount,
                    'bank_transaction_id' => $bankTxId ?: null,
                    'bank_raw_payload' => $rawPayload,
                ]);

                $locked->refresh();
                $csId = $this->tryAutoCreateCustomerService($locked, $source);

                return [
                    'ok' => true,
                    'status' => $csId ? 'paid_and_activated' : 'paid_only',
                    'cs_id' => $csId,
                ];
            });
        } catch (\Throwable $e) {
            Log::error('PaymentService: markOrderPaid transaction failed — rolled back', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'source' => $source,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['ok' => false, 'status' => 'transaction_failed', 'error' => $e->getMessage()];
        }
    }

    /**
     * Mark cả LÔ đơn (group_code) là đã thanh toán + activate/create N CustomerService.
     *
     * Khách CK 1 lần với content chứa GR-XXX → Pay2S match → call method này.
     * Tất cả PO trong group được lock + mark paid trong cùng 1 DB transaction.
     * Nếu 1 đơn fail → rollback toàn bộ → giữ data nhất quán.
     *
     * Số tiền bank transfer được PHÂN BỔ vào từng đơn theo amount riêng của
     * từng đơn (KHÔNG chia đều). Vd group có 2 đơn 100k + 200k, khách CK 300k
     * → đơn 1 ghi paid_amount=100k, đơn 2 ghi paid_amount=200k.
     *
     * @param  string $groupCode  GR-yymmdd-XXX
     * @param  int    $totalAmount  Tổng tiền nhận từ bank
     * @param  string|null $bankTxId  Bank transaction ID (chung cho cả lô)
     * @param  string|null $rawPayload  Raw payload (chung cho cả lô)
     * @param  string $source  'pay2s' | 'manual' | ...
     * @return array{ok: bool, status: string, group_code: string, count: int, orders: array, total_expected: int, delta: int}
     */
    public function markGroupPaid(
        string $groupCode,
        int $totalAmount,
        ?string $bankTxId = null,
        ?string $rawPayload = null,
        string $source = 'pay2s'
    ): array {
        try {
            return DB::transaction(function () use ($groupCode, $totalAmount, $bankTxId, $rawPayload, $source) {
                // Lock TẤT CẢ PO trong group (để tránh race với webhook khác cùng group_code)
                $orders = PendingOrder::where('group_code', $groupCode)
                    ->lockForUpdate()
                    ->orderBy('order_code')
                    ->get();

                if ($orders->isEmpty()) {
                    return [
                        'ok' => false,
                        'status' => 'group_not_found',
                        'group_code' => $groupCode,
                        'count' => 0,
                        'orders' => [],
                    ];
                }

                $totalExpected = (int) $orders->sum('amount');
                $delta = $totalAmount - $totalExpected;

                $results = [];
                $newlyPaidCount = 0; // số đơn ACTUALLY chuyển pending → paid lần này
                foreach ($orders as $idx => $order) {
                    if ($order->paid_at !== null) {
                        $results[] = [
                            'order_code' => $order->order_code,
                            'status' => 'already_paid',
                            'cs_id' => $order->customer_service_id,
                        ];
                        continue;
                    }

                    // Phân bổ paid_amount = amount riêng của đơn (KHÔNG chia đều).
                    // bankTxId thêm suffix index để unique-constraint không vỡ.
                    $perOrderTxId = $bankTxId ? ($bankTxId . '-' . $idx) : null;

                    $order->update([
                        'paid_at' => now(),
                        'paid_amount' => (int) $order->amount,
                        'bank_transaction_id' => $perOrderTxId,
                        'bank_raw_payload' => $rawPayload, // chung — show toàn payload
                    ]);

                    $order->refresh();
                    $csId = $this->tryAutoCreateCustomerService($order, $source);

                    $results[] = [
                        'order_code' => $order->order_code,
                        'status' => $csId ? 'paid_and_activated' : 'paid_only',
                        'cs_id' => $csId,
                    ];
                    $newlyPaidCount++;
                }

                Log::info('PaymentService: markGroupPaid completed', [
                    'group_code' => $groupCode,
                    'total_amount' => $totalAmount,
                    'total_expected' => $totalExpected,
                    'delta' => $delta,
                    'order_count' => count($results),
                    'newly_paid_count' => $newlyPaidCount,
                    'source' => $source,
                ]);

                return [
                    'ok' => true,
                    // Status 'group_already_paid' khi tất cả đơn đã paid trước đó (Pay2S retry)
                    'status' => $newlyPaidCount > 0 ? 'group_paid' : 'group_already_paid',
                    'group_code' => $groupCode,
                    'count' => count($results),
                    'newly_paid_count' => $newlyPaidCount,
                    'orders' => $results,
                    'total_expected' => $totalExpected,
                    'delta' => $delta,
                ];
            });
        } catch (\Throwable $e) {
            Log::error('PaymentService: markGroupPaid transaction failed — rolled back', [
                'group_code' => $groupCode,
                'source' => $source,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'ok' => false,
                'status' => 'transaction_failed',
                'group_code' => $groupCode,
                'count' => 0,
                'orders' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Hybrid:
     *   - PendingOrder.customer_service_id != null → activate CS pending.
     *   - Còn lại → tạo CS active mới nếu đủ data structured.
     */
    public function tryAutoCreateCustomerService(PendingOrder $order, string $source = 'pay2s'): ?int
    {
        if ($order->customer_service_id) {
            return $this->activatePendingCustomerService($order, $source);
        }

        $missing = array_filter([
            'customer_id' => !$order->customer_id,
            'service_package_id' => !$order->service_package_id,
            'account_email' => empty($order->account_email),
            'duration_days' => !$order->duration_days,
        ]);

        if (!empty($missing)) {
            Log::info('PaymentService: skip auto-create CS (thiếu data structured)', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'source' => $source,
                'missing' => array_keys($missing),
            ]);
            return null;
        }

        return $this->createActiveCustomerService($order, $source);
    }

    private function activatePendingCustomerService(PendingOrder $order, string $source): ?int
    {
        $cs = CustomerService::find($order->customer_service_id);
        if (!$cs) {
            Log::warning('PaymentService: CS link tới đơn không tồn tại — fallback create mới', [
                'order_id' => $order->id,
                'cs_id_orphan' => $order->customer_service_id,
                'source' => $source,
            ]);
            $order->update(['customer_service_id' => null]);
            return $this->createActiveCustomerService($order, $source);
        }

        if ($cs->status === 'active' && $cs->activated_at) {
            return $cs->id;
        }

        $now = now();
        $expiresAt = $now->copy()->addDays((int) ($order->duration_days ?? $cs->duration_days ?? 0));
        $sourceLabel = $source === 'manual' ? 'admin xác nhận thủ công' : 'Pay2S';

        $cs->update([
            'status' => 'active',
            'activated_at' => $now,
            'expires_at' => $expiresAt,
            'internal_notes' => trim(($cs->internal_notes ?? '') . "\n\n💰 Thanh toán xác nhận qua {$sourceLabel} ({$now->format('d/m/Y H:i')})"),
        ]);

        if (!empty($order->profit_amount) && $order->profit_amount > 0 && !$cs->profit) {
            Profit::create([
                'customer_service_id' => $cs->id,
                'profit_amount' => $order->profit_amount,
                'notes' => "Tự tạo từ đơn {$order->order_code} qua {$sourceLabel}",
            ]);
        }

        $order->update(['status' => 'completed']);

        Log::info('PaymentService: activated pending CustomerService', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'customer_service_id' => $cs->id,
            'source' => $source,
        ]);

        return $cs->id;
    }

    private function createActiveCustomerService(PendingOrder $order, string $source): ?int
    {
        $now = now();
        $expiresAt = $now->copy()->addDays((int) $order->duration_days);
        $sourceLabel = $source === 'manual' ? 'admin xác nhận thủ công' : 'Pay2S webhook';

        $internalNotes = "📋 Tự tạo từ đơn {$order->order_code} qua {$sourceLabel} ({$now->format('d/m/Y H:i')})";
        if (!empty($order->family_code)) {
            $internalNotes .= "\nMã nhóm-gia đình: {$order->family_code}";
        }

        $cs = CustomerService::create([
            'pending_order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'service_package_id' => $order->service_package_id,
            'login_email' => $order->account_email,
            'activated_at' => $now,
            'expires_at' => $expiresAt,
            'status' => 'active',
            'duration_days' => $order->duration_days,
            'warranty_days' => $order->warranty_days,
            'order_amount' => $order->amount,
            'family_code' => $order->family_code,
            'internal_notes' => $internalNotes,
        ]);

        if (!empty($order->profit_amount) && $order->profit_amount > 0) {
            Profit::create([
                'customer_service_id' => $cs->id,
                'profit_amount' => $order->profit_amount,
                'notes' => "Tự tạo từ đơn {$order->order_code} qua {$sourceLabel}",
            ]);
        }

        $order->update([
            'customer_service_id' => $cs->id,
            'status' => 'completed',
        ]);

        Log::info('PaymentService: created active CustomerService', [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'customer_service_id' => $cs->id,
            'source' => $source,
        ]);

        return $cs->id;
    }
}
