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
