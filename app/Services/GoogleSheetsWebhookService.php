<?php

namespace App\Services;

use App\Models\ResourceAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sync Resource Account → Google Sheet qua Apps Script web app webhook.
 *
 * Setup (user side, lần đầu):
 *   1. Tạo Google Sheet mới với header: Ngày | Category | Email | Password | Note | Status | DB_ID
 *   2. Sheet → Extensions → Apps Script → paste script (xem README hoặc admin docs)
 *   3. Deploy as Web App → copy URL → set GOOGLE_SHEETS_WEBHOOK_URL trong .env
 *   4. Đặt 1 token bí mật → set GOOGLE_SHEETS_WEBHOOK_TOKEN trong .env (cùng giá trị
 *      với SECRET_TOKEN trong Apps Script)
 *
 * Endpoint expect POST JSON:
 *   { token, action: "append_resource", data: { ... fields... } }
 *
 * Response success: { ok: true, row: N }
 * Response error: { ok: false, error: "..." }
 */
class GoogleSheetsWebhookService
{
    private string $url;
    private string $token;

    public function __construct()
    {
        $this->url = (string) env('GOOGLE_SHEETS_WEBHOOK_URL', '');
        $this->token = (string) env('GOOGLE_SHEETS_WEBHOOK_TOKEN', '');
    }

    public function isEnabled(): bool
    {
        return $this->url !== '' && $this->token !== '';
    }

    /**
     * Push 1 ResourceAccount lên Google Sheet (append row).
     * Return true nếu success, false nếu fail (đã log).
     */
    public function pushResourceAccount(ResourceAccount $account): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $account->loadMissing('category');

            $payload = [
                'token' => $this->token,
                'action' => 'append_resource',
                'data' => [
                    'date' => $account->created_at?->format('Y-m-d H:i:s'),
                    'category' => $account->category?->name ?? '',
                    'email' => $account->email ?? '',
                    'username' => $account->username ?? '',
                    'password' => $account->password ?? '',
                    'notes' => $account->notes ?? '',
                    'status' => $account->is_available ? 'available' : 'sold',
                    'db_id' => $account->id,
                ],
            ];

            $resp = Http::timeout(10)
                ->connectTimeout(5)
                ->post($this->url, $payload);

            if (!$resp->successful()) {
                Log::warning('GoogleSheets webhook: non-2xx', [
                    'status' => $resp->status(),
                    'body' => $resp->body(),
                ]);
                return false;
            }

            $json = $resp->json();
            if (!($json['ok'] ?? false)) {
                Log::warning('GoogleSheets webhook: error response', ['response' => $json]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('GoogleSheets webhook: exception', [
                'error' => $e->getMessage(),
                'account_id' => $account->id,
            ]);
            return false;
        }
    }
}
