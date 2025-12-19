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
        if (!$formattedAmount || !is_string($formattedAmount)) {
            return 0;
        }

        // Convert to string if not already
        $formattedAmount = (string) $formattedAmount;

        // Remove currency symbols, spaces, and letters, keep only digits, dots, and commas
        $cleaned = preg_replace('/[^\d.,]/', '', $formattedAmount);

        // Check if this looks like a decimal number (has comma/dot at the end for cents)
        // VD: 1,000.50 hoặc 1.000,50
        if (preg_match('/[.,]\d{1,2}$/', $cleaned)) {
            // This has decimal places
            // If it ends with comma (European style): 1.000,50 -> 1000.50
            if (preg_match('/,\d{1,2}$/', $cleaned)) {
                $cleaned = str_replace('.', '', $cleaned); // Remove thousand separators (dots)
                $cleaned = str_replace(',', '.', $cleaned); // Convert comma to dot for decimal
            }
            // If it ends with dot (US style): 1,000.50 -> 1000.50
            else {
                $cleaned = preg_replace('/,(?=\d{3})/', '', $cleaned); // Remove thousand separators (commas)
            }
        } else {
            // This is just a whole number with thousand separators
            // Remove all dots and commas as they are just separators
            $cleaned = str_replace(['.', ','], '', $cleaned);
        }

        return (float) $cleaned;
    }
}
