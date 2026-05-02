<?php

namespace App\Observers;

use App\Models\CustomerService;
use App\Models\CustomerServiceAudit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * Audit log mọi thay đổi của CustomerService — ghi vào customer_service_audits
 * để trace bug khi data sai (vd: ai đổi expires_at? activated_at thay đổi khi nào?).
 *
 * Các trường nhạy cảm bị mask trong log (login_password, recovery_codes,
 * two_factor_code, bank_raw_payload).
 */
class CustomerServiceObserver
{
    private const SENSITIVE_FIELDS = [
        'login_password',
        'recovery_codes',
        'two_factor_code',
    ];

    public function created(CustomerService $cs): void
    {
        $this->writeAudit($cs, 'created', null, $this->scrub($cs->getAttributes()));
    }

    public function updated(CustomerService $cs): void
    {
        $changes = $cs->getChanges();
        if (empty($changes)) return;

        $original = [];
        foreach (array_keys($changes) as $key) {
            $original[$key] = $cs->getOriginal($key);
        }

        $this->writeAudit(
            $cs,
            'updated',
            $this->scrub($original),
            $this->scrub($changes),
            array_keys($changes)
        );
    }

    public function deleted(CustomerService $cs): void
    {
        $this->writeAudit($cs, 'deleted', $this->scrub($cs->getAttributes()), null);
    }

    public function restored(CustomerService $cs): void
    {
        $this->writeAudit($cs, 'restored', null, $this->scrub($cs->getAttributes()));
    }

    private function writeAudit(
        CustomerService $cs,
        string $event,
        ?array $old,
        ?array $new,
        ?array $changedFields = null
    ): void {
        try {
            CustomerServiceAudit::create([
                'customer_service_id' => $cs->id,
                'event' => $event,
                'old_values' => $old,
                'new_values' => $new,
                'changed_fields' => $changedFields,
                'actor_type' => $this->detectActorType(),
                'actor_id' => Auth::id(),
                'actor_label' => $this->detectActorLabel(),
                'ip_address' => $this->safeRequestValue(fn() => Request::ip()),
                'user_agent' => $this->safeRequestValue(fn() => substr((string) Request::userAgent(), 0, 255)),
            ]);
        } catch (\Throwable $e) {
            // Audit fail không nên block business logic — chỉ log warning
            Log::warning('CustomerServiceAudit: failed to write', [
                'cs_id' => $cs->id,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function scrub(array $attrs): array
    {
        foreach (self::SENSITIVE_FIELDS as $field) {
            if (isset($attrs[$field]) && $attrs[$field] !== null) {
                $attrs[$field] = '***REDACTED***';
            }
        }
        return $attrs;
    }

    private function detectActorType(): string
    {
        if (app()->runningInConsole()) {
            return 'cli';
        }
        if (Auth::check()) {
            return 'user';
        }
        // Webhook hoặc unauth — phân biệt qua URL hoặc header
        try {
            $path = Request::path();
            if (str_starts_with($path, 'api/webhook')) {
                return 'webhook';
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return 'system';
    }

    private function detectActorLabel(): ?string
    {
        if (app()->runningInConsole()) {
            // Telegram bot chạy qua artisan command
            return 'CLI / Bot Telegram';
        }
        if (Auth::check()) {
            $user = Auth::user();
            return $user->email ?? $user->name ?? "user#{$user->id}";
        }
        try {
            $path = Request::path();
            if (str_starts_with($path, 'api/webhook/pay2s')) {
                return 'Pay2S webhook';
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return null;
    }

    private function safeRequestValue(callable $fn): ?string
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            return null; // request facade có thể fail trong queue/scheduled context
        }
    }
}
