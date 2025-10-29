<?php

namespace App\Http\Controllers;

use App\Models\ZaloAccount;
use App\Models\TargetGroup;
use App\Models\MessageCampaign;
use App\Models\MessageLog;
use App\Models\ConversionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZaloDashboardController extends Controller
{
    /**
     * Display the Zalo marketing dashboard
     */
    public function index(Request $request)
    {
        // Date range filter
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Overall statistics
        $stats = [
            'total_accounts' => ZaloAccount::where('status', 'active')->count(),
            'total_groups' => TargetGroup::where('status', 'active')->count(),
            'active_campaigns' => MessageCampaign::where('status', 'active')->count(),
            'total_messages_sent' => MessageLog::whereBetween('sent_at', [$startDate, $endDate])->count(),
            'total_conversions' => ConversionLog::whereBetween('joined_at', [$startDate, $endDate])->count(),
            'overall_conversion_rate' => $this->calculateOverallConversionRate($startDate, $endDate),
        ];

        // Daily message statistics
        $dailyMessages = $this->getDailyMessageStats($startDate, $endDate);

        // Daily conversion statistics
        $dailyConversions = $this->getDailyConversionStats($startDate, $endDate);

        // Top performing campaigns
        $topCampaigns = $this->getTopCampaigns(5);

        // Account performance
        $accountPerformance = $this->getAccountPerformance();

        // Recent activities
        $recentMessages = MessageLog::with(['campaign', 'zaloAccount', 'groupMember'])
            ->latest('sent_at')
            ->limit(10)
            ->get();

        $recentConversions = ConversionLog::with(['campaign', 'groupMember', 'ownGroup'])
            ->latest('joined_at')
            ->limit(10)
            ->get();

        return view('zalo.dashboard', compact(
            'stats',
            'dailyMessages',
            'dailyConversions',
            'topCampaigns',
            'accountPerformance',
            'recentMessages',
            'recentConversions',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Calculate overall conversion rate
     */
    private function calculateOverallConversionRate($startDate, $endDate): float
    {
        $totalDelivered = MessageLog::whereBetween('sent_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->count();

        if ($totalDelivered === 0) {
            return 0;
        }

        $totalConverted = ConversionLog::whereBetween('joined_at', [$startDate, $endDate])
            ->count();

        return round(($totalConverted / $totalDelivered) * 100, 2);
    }

    /**
     * Get daily message statistics
     */
    private function getDailyMessageStats($startDate, $endDate)
    {
        return MessageLog::selectRaw('
                DATE(sent_at) as date,
                COUNT(*) as total,
                SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed
            ')
            ->whereBetween('sent_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Get daily conversion statistics
     */
    private function getDailyConversionStats($startDate, $endDate)
    {
        return ConversionLog::selectRaw('
                DATE(joined_at) as date,
                COUNT(*) as count
            ')
            ->whereBetween('joined_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Get top performing campaigns
     */
    private function getTopCampaigns($limit = 5)
    {
        return MessageCampaign::with(['targetGroup', 'ownGroup'])
            ->where('status', '!=', 'draft')
            ->orderBy('conversion_rate', 'desc')
            ->orderBy('total_converted', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get account performance statistics
     */
    private function getAccountPerformance()
    {
        return ZaloAccount::select('zalo_accounts.*')
            ->selectRaw('
                (SELECT COUNT(*) FROM message_logs WHERE message_logs.zalo_account_id = zalo_accounts.id) as total_messages,
                (SELECT COUNT(*) FROM message_logs WHERE message_logs.zalo_account_id = zalo_accounts.id AND status = "delivered") as successful_messages
            ')
            ->where('status', 'active')
            ->get()
            ->map(function ($account) {
                $account->success_rate = $account->total_messages > 0 
                    ? round(($account->successful_messages / $account->total_messages) * 100, 2)
                    : 0;
                return $account;
            });
    }

    /**
     * Get conversion funnel data
     */
    public function conversionFunnel(Request $request)
    {
        $campaignId = $request->input('campaign_id');

        $query = MessageLog::query();
        
        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }

        $funnelData = [
            'total_sent' => $query->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'contacted' => (clone $query)->whereHas('groupMember', function($q) {
                $q->whereIn('status', ['contacted', 'converted']);
            })->count(),
            'converted' => ConversionLog::when($campaignId, function($q) use ($campaignId) {
                $q->where('campaign_id', $campaignId);
            })->count(),
        ];

        return response()->json($funnelData);
    }
}

