<?php

if (!function_exists('format_currency')) {
    /**
     * Format số tiền theo định dạng Việt Nam
     *
     * @param float|int $amount
     * @return string
     */
    function format_currency($amount)
    {
        return number_format($amount, 0, ',', '.') . ' VNĐ';
    }
}

if (!function_exists('format_number')) {
    /**
     * Format số theo định dạng Việt Nam (không có đơn vị)
     *
     * @param float|int $amount
     * @return string
     */
    function format_number($amount)
    {
        return number_format($amount, 0, ',', '.');
    }
}
