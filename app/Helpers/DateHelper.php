<?php

if (!function_exists('formatDate')) {
    /**
     * Format date theo timezone Việt Nam
     *
     * @param \Carbon\Carbon|string|null $date
     * @param string $format
     * @return string
     */
    function formatDate($date, $format = 'd/m/Y H:i:s')
    {
        if (!$date) {
            return 'N/A';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        } elseif ($date instanceof \DateTime) {
            $date = \Carbon\Carbon::instance($date);
        }

        return $date->setTimezone('Asia/Ho_Chi_Minh')->format($format);
    }
}

if (!function_exists('formatDateShort')) {
    /**
     * Format date ngắn gọn
     *
     * @param \Carbon\Carbon|string|null $date
     * @return string
     */
    function formatDateShort($date)
    {
        return formatDate($date, 'd/m/Y');
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format datetime đầy đủ
     *
     * @param \Carbon\Carbon|string|null $date
     * @return string
     */
    function formatDateTime($date)
    {
        return formatDate($date, 'd/m/Y H:i:s');
    }
}

if (!function_exists('formatTimeAgo')) {
    /**
     * Format thời gian tương đối (vừa, 5 phút trước, etc.)
     *
     * @param \Carbon\Carbon|string|null $date
     * @return string
     */
    function formatTimeAgo($date)
    {
        if (!$date) {
            return 'N/A';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        } elseif ($date instanceof \DateTime) {
            $date = \Carbon\Carbon::instance($date);
        }

        $date = $date->setTimezone('Asia/Ho_Chi_Minh');
        $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');

        $diffInMinutes = $date->diffInMinutes($now);
        $diffInHours = $date->diffInHours($now);
        $diffInDays = $date->diffInDays($now);

        if ($diffInMinutes < 1) {
            return 'Vừa xong';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . ' phút trước';
        } elseif ($diffInHours < 24) {
            return $diffInHours . ' giờ trước';
        } elseif ($diffInDays < 7) {
            return $diffInDays . ' ngày trước';
        } else {
            return $date->format('d/m/Y H:i');
        }
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format tiền tệ theo định dạng Việt Nam với dấu chấm phân cách hàng nghìn
     *
     * @param float|int|string|null $amount
     * @param string $currency
     * @param bool $showCurrency
     * @return string
     */
    function formatCurrency($amount, $currency = 'VND', $showCurrency = true)
    {
        if ($amount === null || $amount === '') {
            return '0';
        }

        // Convert to number
        $amount = (float) $amount;

        // Format with dot as thousand separator
        $formatted = number_format($amount, 0, ',', '.');

        // Add currency if requested
        if ($showCurrency) {
            $formatted .= ' ' . $currency;
        }

        return $formatted;
    }
}

if (!function_exists('formatPrice')) {
    /**
     * Format giá tiền ngắn gọn (alias cho formatCurrency)
     *
     * @param float|int|string|null $amount
     * @return string
     */
    function formatPrice($amount)
    {
        return formatCurrency($amount, 'đ', true);
    }
}

if (!function_exists('formatMoney')) {
    /**
     * Format tiền không có đơn vị (chỉ số)
     *
     * @param float|int|string|null $amount
     * @return string
     */
    function formatMoney($amount)
    {
        return formatCurrency($amount, '', false);
    }
}

if (!function_exists('parseCurrency')) {
    /**
     * Parse formatted currency string back to number
     *
     * @param string|null $formattedAmount
     * @return float
     */
    function parseCurrency($formattedAmount)
    {
        if ($formattedAmount === null || $formattedAmount === '') {
            return 0;
        }

        if (!is_string($formattedAmount)) {
            return (float) $formattedAmount;
        }

        $cleaned = preg_replace('/[^\d.,]/', '', $formattedAmount);
        $cleaned = str_replace('.', '', $cleaned);
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }
}

if (!function_exists('parseShortAmount')) {
    /**
     * Parse số tiền dạng ngắn: "100k", "1.5tr", "200000", "200.000".
     *
     * @param mixed $input
     * @return int
     */
    function parseShortAmount($input): int
    {
        if ($input === null || $input === '') return 0;

        // Chỉ trust is_numeric cho int/float thực sự, KHÔNG dùng cho string
        // (PHP coi '100.000' là numeric = 100, sẽ sai cho format VN)
        if (is_int($input) || is_float($input)) {
            return parseShortAmountClamp((int) round((float) $input));
        }

        $s = strtolower(trim((string) $input));
        if (preg_match('/^([\d.,]+)\s*(k|nghìn|nghin|tr|triệu|trieu|m)?\s*$/u', $s, $m)) {
            $unit = $m[2] ?? '';

            if ($unit === '') {
                // Không có đơn vị → bỏ tất cả dấu chấm/phẩy (đều là thousand separator vì VND không có decimal)
                // Hỗ trợ "100.000", "100,000", "1.000.000"
                return parseShortAmountClamp((int) round((float) str_replace(['.', ','], '', $m[1])));
            }

            // Có đơn vị → parse số thập phân (vd. "1.5tr", "1,5tr")
            $num = (float) str_replace(',', '.', $m[1]);
            $val = match ($unit) {
                'k', 'nghìn', 'nghin' => (int) round($num * 1_000),
                'tr', 'triệu', 'trieu', 'm' => (int) round($num * 1_000_000),
            };
            return parseShortAmountClamp($val);
        }
        return parseShortAmountClamp((int) round((float) parseCurrency($s)));
    }
}

if (!function_exists('parseShortAmountClamp')) {
    /**
     * Clamp về [0, 500_000_000] đồng. Negative → 0; vượt 500tr → 0
     * (return 0 thay vì cap để báo input sai). Caller check ≤ 0 để reject.
     */
    function parseShortAmountClamp(int $val): int
    {
        if ($val < 0) return 0;
        if ($val > 500_000_000) return 0; // 500 triệu — phòng typo "100kkk" hoặc paste lỗi
        return $val;
    }
}

if (!function_exists('formatShortAmount')) {
    /**
     * Format số tiền sang dạng ngắn nếu tròn:
     *   100000   -> "100k"
     *   1500000  -> "1.5tr"
     *   1000000  -> "1tr"
     *   1234     -> "1,234đ"  (không tròn → fallback)
     *
     * @param int|float|string|null $amount
     * @return string
     */
    function formatShortAmount($amount): string
    {
        $a = (int) round((float) $amount);
        if ($a === 0) return '0đ';

        $abs = abs($a);
        $sign = $a < 0 ? '-' : '';

        // ≥ 1 triệu
        if ($abs >= 1_000_000) {
            $value = $abs / 1_000_000;
            // Chỉ dùng "tr" nếu chia hết cho 100k (vd 1.5tr = 1,500,000) hoặc tròn triệu
            if ($abs % 100_000 === 0) {
                $str = rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
                return $sign . $str . 'tr';
            }
        }

        // ≥ 1k, chia hết cho 1000
        if ($abs >= 1_000 && $abs % 1_000 === 0) {
            return $sign . ($abs / 1000) . 'k';
        }

        // Fallback: format đầy đủ
        return $sign . number_format($abs, 0, ',', '.') . 'đ';
    }
}
