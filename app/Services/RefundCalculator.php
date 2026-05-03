<?php

namespace App\Services;

use App\Models\CustomerService;

/**
 * Tính tiền hoàn cho 1 CustomerService theo % thời gian còn lại.
 *
 * Logic:
 *   - CS chưa kích hoạt → hoàn FULL order_amount.
 *   - CS active → hoàn `order_amount × (days_remaining / total_days)`.
 *   - CS đã hết hạn → hoàn 0.
 *   - CS đã cancelled / đã refunded → trả về error (không cho tính lại).
 *
 * Service KHÔNG ghi DB — chỉ tính. Confirm refund (lưu DB) ở RefundController.
 */
class RefundCalculator
{
    /**
     * Compute refund cho 1 CS.
     *
     * @return array{
     *   ok: bool,
     *   reason?: string,                  // mã lỗi nếu ok=false
     *   mode?: string,                    // 'full' | 'partial' | 'expired'
     *   refund_amount?: int,
     *   order_amount?: int,
     *   total_days?: int,
     *   days_used?: int,
     *   days_remaining?: int,
     *   percent_remaining?: float,
     *   reason_label?: string,            // label tiếng Việt giải thích
     * }
     */
    public function compute(CustomerService $cs): array
    {
        // Validate state
        if ($cs->refunded_at !== null) {
            return ['ok' => false, 'reason' => 'already_refunded'];
        }
        if ($cs->status === 'cancelled') {
            return ['ok' => false, 'reason' => 'already_cancelled'];
        }
        if (empty($cs->order_amount) || $cs->order_amount <= 0) {
            return ['ok' => false, 'reason' => 'no_order_amount'];
        }

        $orderAmount = (int) $cs->order_amount;

        // CASE 1: Chưa kích hoạt (CS pending) — hoàn full
        if (!$cs->activated_at) {
            return [
                'ok' => true,
                'mode' => 'full',
                'refund_amount' => $orderAmount,
                'order_amount' => $orderAmount,
                'total_days' => null,
                'days_used' => 0,
                'days_remaining' => null,
                'percent_remaining' => 100.0,
                'reason_label' => 'Đơn chưa kích hoạt — hoàn 100% số tiền đơn',
            ];
        }

        // CASE 2: Không có expires_at (data lỗi) — không thể tính %
        if (!$cs->expires_at) {
            return ['ok' => false, 'reason' => 'no_expires_at'];
        }

        $now = now();
        $activatedAt = $cs->activated_at->copy()->startOfDay();
        $expiresAt = $cs->expires_at->copy()->startOfDay();
        $today = $now->copy()->startOfDay();

        // CASE 3: Đã hết hạn — không hoàn
        if ($today->gte($expiresAt)) {
            $totalDays = max(0, $activatedAt->diffInDays($expiresAt));
            return [
                'ok' => true,
                'mode' => 'expired',
                'refund_amount' => 0,
                'order_amount' => $orderAmount,
                'total_days' => $totalDays,
                'days_used' => $totalDays,
                'days_remaining' => 0,
                'percent_remaining' => 0.0,
                'reason_label' => 'Đơn đã hết hạn — không có gì để hoàn',
            ];
        }

        // CASE 4: Đang active — tính theo % thời gian còn lại
        $totalDays = max(1, $activatedAt->diffInDays($expiresAt));
        $daysUsed = max(0, min($totalDays, $activatedAt->diffInDays($today)));
        $daysRemaining = max(0, $totalDays - $daysUsed);
        $percentRemaining = round(($daysRemaining / $totalDays) * 100, 2);
        $refundAmount = (int) round($orderAmount * ($daysRemaining / $totalDays));

        return [
            'ok' => true,
            'mode' => 'partial',
            'refund_amount' => $refundAmount,
            'order_amount' => $orderAmount,
            'total_days' => $totalDays,
            'days_used' => $daysUsed,
            'days_remaining' => $daysRemaining,
            'percent_remaining' => $percentRemaining,
            'reason_label' => "Đã dùng {$daysUsed}/{$totalDays} ngày → hoàn {$percentRemaining}% (≈ " . number_format($refundAmount, 0, ',', '.') . "đ)",
        ];
    }
}
