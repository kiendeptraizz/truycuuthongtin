<?php

namespace App\Services;

use App\Models\CustomerService;
use App\Models\CustomerServiceWarranty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service tạo record bảo hành cho 1 CustomerService — atomic update CS
 * (đổi TK / gia hạn) + lưu lịch sử trong customer_service_warranties.
 *
 * Audit của CS tự ghi qua observer (CustomerServiceObserver).
 */
class WarrantyService
{
    /**
     * Apply 1 lần bảo hành cho CS.
     *
     * @param CustomerService $cs
     * @param array $data {
     *   replacement_email?: string|null,
     *   replacement_password?: string|null,
     *   extended_days?: int|null,
     *   note: string,
     *   actor_type?: string,           // 'user' | 'bot'
     *   actor_id?: int|null,
     *   actor_label?: string|null,     // vd 'admin@truycuu' / 'telegram:7351...'
     * }
     * @return array{ok: bool, warranty_id?: int, error?: string}
     */
    public function apply(CustomerService $cs, array $data): array
    {
        if (empty(trim($data['note'] ?? ''))) {
            return ['ok' => false, 'error' => 'Ghi chú bảo hành là bắt buộc.'];
        }
        if ($cs->status === 'cancelled') {
            return ['ok' => false, 'error' => 'Đơn đã huỷ — không thể bảo hành.'];
        }

        try {
            return DB::transaction(function () use ($cs, $data) {
                $newEmail = !empty($data['replacement_email']) ? trim($data['replacement_email']) : null;
                $newPassword = !empty($data['replacement_password']) ? $data['replacement_password'] : null;
                $extendedDays = isset($data['extended_days']) && $data['extended_days'] > 0
                    ? (int) $data['extended_days']
                    : null;

                // Update CS nếu có thay đổi TK / gia hạn
                $csUpdates = [];
                if ($newEmail) {
                    $csUpdates['login_email'] = $newEmail;
                }
                if ($newPassword) {
                    $csUpdates['login_password'] = $newPassword;
                }
                if ($extendedDays && $cs->expires_at) {
                    $csUpdates['expires_at'] = $cs->expires_at->copy()->addDays($extendedDays);
                }
                if (!empty($csUpdates)) {
                    $cs->update($csUpdates);
                }

                // Tạo record lịch sử
                $warranty = CustomerServiceWarranty::create([
                    'customer_service_id' => $cs->id,
                    'replacement_email' => $newEmail,
                    'replacement_password' => $newPassword,
                    'extended_days' => $extendedDays,
                    'note' => trim($data['note']),
                    'actor_type' => $data['actor_type'] ?? 'user',
                    'actor_id' => $data['actor_id'] ?? null,
                    'actor_label' => $data['actor_label'] ?? null,
                ]);

                Log::info('Warranty applied', [
                    'cs_id' => $cs->id,
                    'order_code' => $cs->order_code,
                    'warranty_id' => $warranty->id,
                    'changed_email' => (bool) $newEmail,
                    'extended_days' => $extendedDays,
                    'actor_type' => $data['actor_type'] ?? 'user',
                ]);

                return ['ok' => true, 'warranty_id' => $warranty->id];
            });
        } catch (\Throwable $e) {
            Log::error('WarrantyService: apply failed', [
                'cs_id' => $cs->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
