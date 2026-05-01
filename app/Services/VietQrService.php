<?php

namespace App\Services;

/**
 * Helper sinh URL ảnh QR VietQR (NAPAS 247).
 *
 * Dùng API miễn phí của vietqr.io: https://img.vietqr.io/image/...
 * Trả về 1 URL PNG có thể nhúng trực tiếp vào <img> hoặc Telegram sendPhoto.
 */
class VietQrService
{
    /**
     * Build URL ảnh QR cho 1 giao dịch.
     *
     * @param int|null $amount Số tiền (VND, không có dấu phẩy/chấm)
     * @param string|null $addInfo Nội dung chuyển khoản (vd. "DH-260501-001")
     */
    public function buildQrUrl(?int $amount = null, ?string $addInfo = null): string
    {
        $bankCode = config('payment.bank_code');
        $accountNumber = config('payment.account_number');
        $accountName = config('payment.account_name');
        $template = config('payment.qr_template', 'compact2');

        $base = "https://img.vietqr.io/image/{$bankCode}-{$accountNumber}-{$template}.png";
        $params = [];
        if ($amount !== null && $amount > 0) {
            $params['amount'] = $amount;
        }
        if ($addInfo) {
            $params['addInfo'] = $addInfo;
        }
        if ($accountName) {
            $params['accountName'] = $accountName;
        }

        return $params ? $base . '?' . http_build_query($params) : $base;
    }

    public function bankShortName(): string
    {
        return config('payment.bank_short_name', '');
    }

    public function accountNumber(): string
    {
        return config('payment.account_number', '');
    }

    public function accountName(): string
    {
        return config('payment.account_name', '');
    }
}
