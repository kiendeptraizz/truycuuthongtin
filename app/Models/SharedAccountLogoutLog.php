<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SharedAccountLogoutLog extends Model
{
    protected $fillable = [
        'login_email',
        'service_package_name',
        'logout_at',
        'performed_by',
        'reason',
        'notes',
        'affected_customers',
        'affected_count',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'logout_at' => 'datetime',
        'affected_customers' => 'array',
        'affected_count' => 'integer',
    ];

    /**
     * Scope để lấy logs theo email
     */
    public function scopeForEmail($query, $email)
    {
        return $query->where('login_email', $email);
    }

    /**
     * Scope để lấy logs gần đây
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('logout_at', '>=', now()->subDays($days));
    }

    /**
     * Accessor để format thời gian logout
     */
    protected function logoutAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->logout_at ? $this->logout_at->format('d/m/Y H:i:s') : null,
        );
    }

    /**
     * Accessor để format danh sách khách hàng bị ảnh hưởng
     */
    protected function affectedCustomersFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->affected_customers || !is_array($this->affected_customers)) {
                    return [];
                }
                
                return collect($this->affected_customers)->map(function ($customer) {
                    return [
                        'name' => $customer['name'] ?? 'N/A',
                        'email' => $customer['email'] ?? 'N/A',
                        'phone' => $customer['phone'] ?? 'N/A',
                        'expires_at' => $customer['expires_at'] ?? null,
                    ];
                });
            }
        );
    }

    /**
     * Tạo log logout mới
     */
    public static function createLogoutLog($email, $servicePackageName, $performedBy, $reason = null, $notes = null, $affectedCustomers = [])
    {
        return self::create([
            'login_email' => $email,
            'service_package_name' => $servicePackageName,
            'logout_at' => now(),
            'performed_by' => $performedBy,
            'reason' => $reason,
            'notes' => $notes,
            'affected_customers' => $affectedCustomers,
            'affected_count' => count($affectedCustomers),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
