<?php

namespace App\Services;

use App\Models\BotProductMap;
use App\Models\Customer;
use App\Models\PendingOrder;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Nhận 1 đơn bán TỰ ĐỘNG (bot Telegram / web shop Python) và ghi vào CRM:
 *   - Tìm/tạo Customer (theo SĐT → email).
 *   - Ánh xạ product bot → ServicePackage (tạo mới trong danh mục "Bán tự động" nếu chưa có).
 *   - Tạo PendingOrder rồi ủy thác PaymentService::markOrderPaid để tạo CustomerService + Profit
 *     (tái dùng nguyên logic atomic/idempotent/lợi nhuận của CRM).
 *
 * Idempotent theo order_code bot (namespaced 'BOT-<code>') — gọi lại nhiều lần không tạo trùng.
 */
class BotSaleIngestService
{
    /** Kênh bán tự động hợp lệ. */
    public const CHANNELS = ['shop_web', 'bot_le', 'bot_si', 'dropship'];

    public function __construct(private PaymentService $payment)
    {
    }

    /**
     * @param array $data payload đơn (xem BotSaleController để biết cấu trúc).
     * @return array{ok: bool, status?: string, error?: string, order_code?: string, customer_service_id?: int|null}
     */
    public function ingest(array $data): array
    {
        $orderCode = trim((string) ($data['order_code'] ?? ''));
        if ($orderCode === '') {
            return ['ok' => false, 'error' => 'missing_order_code'];
        }

        $amount = (int) round((float) ($data['amount'] ?? 0));
        if ($amount <= 0) {
            return ['ok' => false, 'error' => 'invalid_amount'];
        }

        $product = (array) ($data['product'] ?? []);
        $botPid = trim((string) ($product['id'] ?? ''));
        if ($botPid === '') {
            return ['ok' => false, 'error' => 'missing_product_id'];
        }

        $channel = (string) ($data['channel'] ?? '');
        if (!in_array($channel, self::CHANNELS, true)) {
            $channel = 'shop_web';
        }

        // Namespaced để không đụng mã DH-/GR- của CRM và làm khóa idempotent.
        $poCode = 'BOT-' . $orderCode;

        $existing = PendingOrder::where('order_code', $poCode)->first();
        if ($existing) {
            return [
                'ok' => true,
                'status' => 'already_ingested',
                'order_code' => $poCode,
                'customer_service_id' => $existing->customer_service_id,
            ];
        }

        $paidAt = $this->parseDate($data['paid_at'] ?? null); // null → PaymentService dùng now()
        $profit = (int) round((float) ($data['profit_amount'] ?? 0));
        $durationDays = (int) ($product['duration_days'] ?? 0);
        $warrantyDays = (int) ($product['warranty_days'] ?? ($data['warranty_days'] ?? 0));

        try {
            return DB::transaction(function () use (
                $data, $product, $botPid, $poCode, $orderCode, $amount, $channel,
                $paidAt, $profit, $durationDays, $warrantyDays
            ) {
                $customer = $this->resolveCustomer((array) ($data['customer'] ?? []));
                $package = $this->resolvePackage($botPid, $product);

                // login_email lưu vào CS: ưu tiên account bot đã giao, fallback email KH.
                $accountEmail = trim((string) ($data['account_email'] ?? ''))
                    ?: (string) ($customer->email ?? '')
                    ?: ('bot:' . $orderCode);

                $po = PendingOrder::create([
                    'order_code' => $poCode,
                    'amount' => $amount,
                    'status' => 'pending',
                    // created_via giữ default ('web') — enum chỉ nhận telegram/web;
                    // kênh bán tự động thật lưu ở cột 'channel'.
                    'channel' => $channel,
                    'customer_id' => $customer->id,
                    'service_package_id' => $package->id,
                    'account_email' => $accountEmail,
                    'duration_days' => $durationDays,
                    'warranty_days' => $warrantyDays,
                    'profit_amount' => $profit,
                    'note' => 'Đơn tự động từ bot — mã gốc ' . $orderCode,
                ]);

                $result = $this->payment->markOrderPaid(
                    $po,
                    $amount,
                    $orderCode,
                    json_encode($data, JSON_UNESCAPED_UNICODE),
                    'bot',
                    $paidAt
                );

                Log::info('BotSaleIngest: done', [
                    'order_code' => $poCode,
                    'channel' => $channel,
                    'customer_id' => $customer->id,
                    'service_package_id' => $package->id,
                    'status' => $result['status'] ?? null,
                    'cs_id' => $result['cs_id'] ?? null,
                ]);

                return [
                    'ok' => (bool) ($result['ok'] ?? false),
                    'status' => $result['status'] ?? 'unknown',
                    'order_code' => $poCode,
                    'customer_id' => $customer->id,
                    'service_package_id' => $package->id,
                    'customer_service_id' => $result['cs_id'] ?? null,
                ];
            });
        } catch (\Throwable $e) {
            Log::error('BotSaleIngest: failed', [
                'order_code' => $poCode,
                'error' => $e->getMessage(),
            ]);
            return ['ok' => false, 'error' => 'exception', 'message' => $e->getMessage()];
        }
    }

    /** Tìm khách theo SĐT → email; không có thì tạo mới (customer_code tự sinh). */
    private function resolveCustomer(array $c): Customer
    {
        $phone = $this->normalizePhone((string) ($c['phone'] ?? ''));
        $email = trim((string) ($c['email'] ?? ''));
        $name = trim((string) ($c['name'] ?? '')) ?: 'Khách bot';

        $customer = null;
        if ($phone !== '') {
            $customer = Customer::where('phone', $phone)->first();
        }
        if (!$customer && $email !== '') {
            $customer = Customer::where('email', $email)->first();
        }

        if ($customer) {
            // Bổ sung thông tin còn thiếu, KHÔNG ghi đè cái đã có.
            $fill = [];
            if (empty($customer->phone) && $phone !== '') {
                $fill['phone'] = $phone;
            }
            if (empty($customer->email) && $email !== '') {
                $fill['email'] = $email;
            }
            if ($fill) {
                $customer->update($fill);
            }
            return $customer;
        }

        return Customer::createSafe([
            'name' => $name,
            'phone' => $phone !== '' ? $phone : null,
            'email' => $email !== '' ? $email : null,
            'notes' => 'Tự tạo từ đơn bán tự động (bot/web).',
        ]);
    }

    /** Ánh xạ product bot → ServicePackage; tạo mới trong danh mục "Bán tự động" nếu chưa có. */
    private function resolvePackage(string $botPid, array $product): ServicePackage
    {
        $map = BotProductMap::where('bot_product_id', $botPid)->first();
        if ($map && $map->servicePackage) {
            return $map->servicePackage;
        }

        $pkg = ServicePackage::create([
            'category_id' => $this->botCategoryId(),
            'name' => (string) ($product['name'] ?? $botPid),
            'account_type' => $this->accountTypeLabel((string) ($product['account_type'] ?? '')),
            'default_duration_days' => ((int) ($product['duration_days'] ?? 0)) ?: null,
            'price' => (int) round((float) ($product['price'] ?? 0)),
            'cost_price' => (int) round((float) ($product['cost_price'] ?? 0)),
            'description' => 'Đồng bộ tự động từ bot (' . $botPid . ').',
            'is_active' => true,
        ]);

        BotProductMap::create([
            'bot_product_id' => $botPid,
            'service_package_id' => $pkg->id,
            'last_name' => (string) ($product['name'] ?? ''),
        ]);

        return $pkg;
    }

    private function botCategoryId(): int
    {
        $name = config('services.bot_ingest.category_name', '🤖 Bán tự động (Bot/Web)');
        $cat = ServiceCategory::firstOrCreate(
            ['name' => $name],
            ['description' => 'Sản phẩm bán tự động qua bot Telegram & web shop.']
        );
        return (int) $cat->id;
    }

    private function accountTypeLabel(string $t): ?string
    {
        return match ($t) {
            'chung' => 'Dùng chung',
            'rieng' => 'Dùng riêng',
            default => $t !== '' ? $t : null,
        };
    }

    private function normalizePhone(string $p): string
    {
        return preg_replace('/[^0-9+]/', '', $p) ?? '';
    }

    private function parseDate($v): ?Carbon
    {
        if (empty($v)) {
            return null;
        }
        try {
            return Carbon::parse($v);
        } catch (\Throwable) {
            return null;
        }
    }
}
