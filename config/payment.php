<?php

/**
 * Cấu hình thanh toán — dùng cho QR VietQR (NAPAS 247).
 *
 * Bank codes (NAPAS):
 *   970422 = MB Bank          970418 = BIDV         970436 = Vietcombank
 *   970407 = Techcombank      970432 = VPBank       970423 = TPBank
 *   970416 = ACB              970437 = HDBank       970426 = MSB
 *   970403 = Sacombank        970448 = OCB          970441 = VietinBank
 *   970421 = VRB              970454 = VietCapital  970440 = SeABank
 *   970433 = VietBank         970428 = NamABank     970430 = PGBank
 *   970412 = PVcomBank        970438 = BaoVietBank  970439 = PublicBank
 *   ...xem đầy đủ tại https://vietqr.io/danh-sach-api
 */
return [
    // Mặc định: TPBank của Đỗ Trung Kiên (sửa qua .env)
    'bank_code' => env('PAYMENT_BANK_CODE', '970423'),
    'bank_short_name' => env('PAYMENT_BANK_SHORT_NAME', 'TPBank'),
    'account_number' => env('PAYMENT_ACCOUNT_NUMBER', '65718042005'),
    'account_name' => env('PAYMENT_ACCOUNT_NAME', 'DO TRUNG KIEN'),

    // Template QR — compact2 = ảnh có brand đẹp; compact = nhỏ gọn; print = lớn
    'qr_template' => env('PAYMENT_QR_TEMPLATE', 'compact2'),
];
