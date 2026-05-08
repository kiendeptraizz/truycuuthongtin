<?php

namespace App\Console\Commands\Concerns;

use App\Models\PendingOrder;

/**
 * Stats + đơn hết hạn cho TelegramListenCommand.
 *
 * Tách trait từ god-class TelegramListenCommand (P1.3 audit).
 *
 * - sendStatsToday: dashboard hôm nay (button 📊 Thống kê)
 * - sendStatsRange: doanh thu N ngày + top dịch vụ (lệnh /dt N)
 * - sendExpirations + buildExpirationsMessage: đơn hết hạn 3 sections
 *   (hôm nay / quá hạn / sắp hết). buildExpirationsMessage public vì
 *   NotifyExpirationsCommand reuse cho scheduled job 9h sáng.
 */
trait HandlesStats
{
    /**
     * "📊 Thống kê" — profit hôm nay + tháng này, đơn paid + pending hôm nay.
     */
    private function sendStatsToday(int|string $chatId): void
    {
        $today = today();
        $startOfMonth = $today->copy()->startOfMonth();

        $profitToday = (float) \App\Models\Profit::whereDate('created_at', $today)->sum('profit_amount');
        $profitMonth = (float) \App\Models\Profit::whereBetween('created_at', [$startOfMonth, $today->copy()->endOfDay()])->sum('profit_amount');

        $paidToday = PendingOrder::where('status', 'completed')->whereDate('paid_at', $today)->count();
        $pendingToday = PendingOrder::where('status', 'pending')->whereDate('created_at', $today)->count();
        $cancelledToday = PendingOrder::where('status', 'cancelled')->whereDate('created_at', $today)->count();

        $newCustomersToday = \App\Models\Customer::whereDate('created_at', $today)->count();

        // Doanh thu (sum amount của đơn đã paid hôm nay) — khác profit
        $revenueToday = (float) PendingOrder::where('status', 'completed')->whereDate('paid_at', $today)->sum('amount');

        $msg = "📊 <b>Thống kê " . $today->format('d/m/Y') . "</b>\n\n"
            . "💵 <b>Lợi nhuận hôm nay:</b> " . formatShortAmount((int) $profitToday) . " (" . number_format($profitToday, 0, ',', '.') . "đ)\n"
            . "📈 <b>Lợi nhuận tháng " . $today->format('m/Y') . ":</b> " . formatShortAmount((int) $profitMonth) . " (" . number_format($profitMonth, 0, ',', '.') . "đ)\n\n"
            . "🛒 <b>Doanh thu hôm nay:</b> " . formatShortAmount((int) $revenueToday) . "\n"
            . "✅ Đơn đã thanh toán: <b>{$paidToday}</b>\n"
            . "⏳ Đơn pending: <b>{$pendingToday}</b>\n"
            . "❌ Đơn đã huỷ: <b>{$cancelledToday}</b>\n\n"
            . "👤 KH mới hôm nay: <b>{$newCustomersToday}</b>";

        $this->bot->sendMessage($chatId, $msg, $this->mainMenuMarkup());
    }

    /**
     * "/dt N" — Stats doanh thu + profit + top dịch vụ trong N ngày qua.
     *
     * Range: [now-N days .. now] (inclusive cả 2 đầu, dùng startOfDay/endOfDay
     * để bao phủ trọn ngày). Top DV gom theo service_package_id (đơn quick
     * chưa fill gói → group vào "Chưa rõ gói").
     */
    private function sendStatsRange(int|string $chatId, int $days): void
    {
        $end = now()->endOfDay();
        $start = now()->subDays($days - 1)->startOfDay(); // N=1 → hôm nay; N=7 → 7 ngày gồm hôm nay

        // Doanh thu (đơn paid trong range)
        $paidQuery = PendingOrder::where('status', 'completed')->whereBetween('paid_at', [$start, $end]);
        $revenue = (float) (clone $paidQuery)->sum('amount');
        $paidCount = (clone $paidQuery)->count();

        // Profit (Profit record tạo trong range — khớp khi đơn được mark paid)
        $profit = (float) \App\Models\Profit::whereBetween('created_at', [$start, $end])->sum('profit_amount');

        // Khách mới
        $newCustomers = \App\Models\Customer::whereBetween('created_at', [$start, $end])->count();

        // Top 5 gói (theo số đơn paid). Eager load servicePackage tránh N+1.
        $topPackages = PendingOrder::query()
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('service_package_id, COUNT(*) as cnt, SUM(amount) as total')
            ->groupBy('service_package_id')
            ->orderByDesc('cnt')
            ->limit(5)
            ->with('servicePackage:id,name')
            ->get();

        $rangeLabel = $days === 1
            ? 'hôm nay (' . $start->format('d/m') . ')'
            : "{$days} ngày qua (" . $start->format('d/m') . ' → ' . $end->format('d/m') . ')';

        $lines = [
            "📊 <b>Doanh thu " . $rangeLabel . "</b>",
            "",
            "💵 Doanh thu: <b>" . formatShortAmount((int) $revenue) . "</b> (" . number_format($revenue, 0, ',', '.') . "đ)",
            "💎 Lợi nhuận: <b>" . formatShortAmount((int) $profit) . "</b> (" . number_format($profit, 0, ',', '.') . "đ)",
            "✅ Đơn paid: <b>{$paidCount}</b>",
            "👤 KH mới: <b>{$newCustomers}</b>",
        ];

        if ($topPackages->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '🏆 <b>Top dịch vụ:</b>';
            $rank = 1;
            foreach ($topPackages as $row) {
                $name = $row->servicePackage->name ?? '(chưa rõ gói)';
                $cnt = (int) $row->cnt;
                $total = (int) $row->total;
                $lines[] = sprintf(
                    "%d. %s — <b>%d đơn</b> · %s",
                    $rank++,
                    e($name),
                    $cnt,
                    formatShortAmount($total)
                );
            }
        }

        if ($days > 1) {
            $avgRev = $revenue / $days;
            $lines[] = '';
            $lines[] = '<i>Trung bình ' . formatShortAmount((int) $avgRev) . '/ngày · gõ <code>/dt 30</code> để xem 30 ngày</i>';
        } else {
            $lines[] = '';
            $lines[] = '<i>Gõ <code>/dt 7</code>, <code>/dt 30</code>, <code>/dt 90</code> để xem range dài hơn</i>';
        }

        $this->bot->sendMessage($chatId, implode("\n", $lines));
    }

    /**
     * "⏰ Hết hạn" — 3 sections: hết hạn hôm nay / đã quá hạn (active chưa update) /
     * sắp hết hạn 3 ngày tới. Cùng dùng cho lệnh thủ công và auto noti 9h.
     */
    private function sendExpirations(int|string $chatId): void
    {
        $msg = $this->buildExpirationsMessage();
        $this->bot->sendMessage($chatId, $msg, $this->mainMenuMarkup());
    }

    /**
     * Build message "đơn hết hạn" — tách ra cho cả lệnh thủ công và scheduled job.
     * Dùng eager load servicePackage + customer để tránh N+1.
     */
    public function buildExpirationsMessage(): string
    {
        $today = today();

        $todayExpire = \App\Models\CustomerService::with(['customer', 'servicePackage'])
            ->whereDate('expires_at', $today)
            ->whereIn('status', ['active', 'expired'])
            ->orderBy('expires_at')
            ->get();

        $overdue = \App\Models\CustomerService::with(['customer', 'servicePackage'])
            ->whereDate('expires_at', '<', $today)
            ->where('status', 'active')
            ->orderBy('expires_at')
            ->limit(15)
            ->get();

        $upcoming = \App\Models\CustomerService::with(['customer', 'servicePackage'])
            ->whereBetween('expires_at', [$today->copy()->addDay()->startOfDay(), $today->copy()->addDays(3)->endOfDay()])
            ->where('status', 'active')
            ->orderBy('expires_at')
            ->get();

        $lines = ["⏰ <b>Đơn hết hạn — " . $today->format('d/m/Y') . "</b>\n"];

        $lines[] = "🔴 <b>Hết hạn HÔM NAY (" . $todayExpire->count() . "):</b>";
        if ($todayExpire->isEmpty()) {
            $lines[] = "<i>— Không có đơn nào</i>";
        } else {
            foreach ($todayExpire as $cs) {
                $lines[] = $this->formatExpirationLine($cs);
            }
        }

        if ($overdue->isNotEmpty()) {
            $lines[] = "\n⚠️ <b>Đã quá hạn nhưng chưa xử lý (" . $overdue->count() . "):</b>";
            foreach ($overdue as $cs) {
                $days = $cs->expires_at ? (int) $cs->expires_at->diffInDays($today) : 0;
                $lines[] = $this->formatExpirationLine($cs) . " <i>(quá {$days}d)</i>";
            }
        }

        $lines[] = "\n🟡 <b>Sắp hết hạn 3 ngày tới (" . $upcoming->count() . "):</b>";
        if ($upcoming->isEmpty()) {
            $lines[] = "<i>— Không có đơn nào</i>";
        } else {
            foreach ($upcoming as $cs) {
                $lines[] = $this->formatExpirationLine($cs);
            }
        }

        return implode("\n", $lines);
    }

    private function formatExpirationLine(\App\Models\CustomerService $cs): string
    {
        $code = $cs->order_code ?? "CS#{$cs->id}";
        $kh = $cs->customer ? $cs->customer->name . ' (' . $cs->customer->customer_code . ')' : '?';
        $pkg = $cs->servicePackage->name ?? '?';
        $exp = $cs->expires_at ? $cs->expires_at->format('d/m') : '?';
        return "• <code>{$code}</code> · <b>{$kh}</b> · {$pkg} · hạn {$exp}";
    }
}
