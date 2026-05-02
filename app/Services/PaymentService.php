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
