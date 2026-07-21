<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BotSaleIngestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Nhận đơn bán TỰ ĐỘNG từ hệ bot Python (web shop + bot lẻ/sỉ + dropship).
 *
 * Route: POST /api/webhook/ingest-sale  (miễn CSRF qua bootstrap/app.php: api/webhook/*)
 * Auth : Header "Authorization: Bearer <BOT_INGEST_TOKEN>" hoặc body {token}.
 *
 * Body (1 đơn):
 *   {
 *     "order_code": "ABC123",                 // mã đơn bot (duy nhất) — khóa idempotent
 *     "channel": "shop_web|bot_le|bot_si|dropship",
 *     "amount": 95000,                        // tiền khách trả (đã trừ giảm giá)
 *     "profit_amount": 70000,                 // lãi = tiền thu - vốn
 *     "paid_at": "2026-07-22T01:00:00+07:00", // (tùy chọn) mặc định thời điểm nhận
 *     "account_email": "login@giao.com",      // (tùy chọn) email/login đã giao khách
 *     "product": { "id":"chatgpt_plus_dung_chung_1_thang", "name":"ChatGPT Plus - 1 tháng",
 *                  "price":95000, "cost_price":25000, "duration_days":30,
 *                  "account_type":"chung", "warranty_days":0 },
 *     "customer": { "phone":"0901234567", "email":"kh@gmail.com", "name":"Nguyễn Văn A" }
 *   }
 * Body (lô — cho backfill): { "orders": [ {..đơn..}, {..đơn..} ] }
 */
class BotSaleController extends Controller
{
    public function __invoke(Request $request, BotSaleIngestService $ingest): JsonResponse
    {
        $expected = (string) config('services.bot_ingest.token', '');
        if ($expected === '') {
            Log::warning('BotSale ingest: chưa cấu hình BOT_INGEST_TOKEN → từ chối');
            return response()->json(['ok' => false, 'error' => 'ingest_disabled'], 503);
        }

        $auth = (string) $request->header('Authorization', '');
        $token = preg_replace('/^(Bearer|Apikey|Token)\s+/i', '', $auth);
        if (!hash_equals($expected, (string) $token) && !hash_equals($expected, (string) $request->input('token'))) {
            return response()->json(['ok' => false, 'error' => 'invalid_token'], 401);
        }

        $data = $request->all();

        // Lô nhiều đơn (dùng cho backfill lịch sử).
        if (isset($data['orders']) && is_array($data['orders'])) {
            $results = [];
            $okCount = 0;
            foreach ($data['orders'] as $o) {
                $r = $ingest->ingest((array) $o);
                $results[] = $r;
                if ($r['ok'] ?? false) {
                    $okCount++;
                }
            }
            return response()->json([
                'ok' => true,
                'total' => count($results),
                'succeeded' => $okCount,
                'results' => $results,
            ]);
        }

        $result = $ingest->ingest($data);
        return response()->json($result, ($result['ok'] ?? false) ? 200 : 422);
    }
}
