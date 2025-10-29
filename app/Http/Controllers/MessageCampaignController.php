<?php

namespace App\Http\Controllers;

use App\Models\MessageCampaign;
use App\Models\TargetGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageCampaignController extends Controller
{
    /**
     * Display a listing of campaigns
     */
    public function index(Request $request)
    {
        $query = MessageCampaign::with(['targetGroup', 'ownGroup']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('zalo.campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new campaign
     */
    public function create()
    {
        $targetGroups = TargetGroup::where('group_type', 'competitor')
            ->where('status', 'active')
            ->get();

        $ownGroups = TargetGroup::where('group_type', 'own')
            ->where('status', 'active')
            ->get();

        return view('zalo.campaigns.create', compact('targetGroups', 'ownGroups'));
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_name' => 'required|string|max:255',
            'target_group_id' => 'required|exists:target_groups,id',
            'own_group_id' => 'nullable|exists:target_groups,id',
            'message_template' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'daily_target' => 'required|integer|min:1|max:500',
            'status' => 'required|in:draft,active,paused,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $campaign = MessageCampaign::create($request->all());

        return redirect()->route('admin.zalo.campaigns.index')
            ->with('success', 'Chiến dịch đã được tạo thành công!');
    }

    /**
     * Display the specified campaign
     */
    public function show(MessageCampaign $campaign)
    {
        $campaign->load(['targetGroup', 'ownGroup', 'messageLogs', 'conversions']);

        // Get daily statistics
        $dailyStats = $this->getDailyStatistics($campaign);

        return view('zalo.campaigns.show', compact('campaign', 'dailyStats'));
    }

    /**
     * Show the form for editing the campaign
     */
    public function edit(MessageCampaign $campaign)
    {
        $targetGroups = TargetGroup::where('group_type', 'competitor')
            ->where('status', 'active')
            ->get();

        $ownGroups = TargetGroup::where('group_type', 'own')
            ->where('status', 'active')
            ->get();

        return view('zalo.campaigns.edit', compact('campaign', 'targetGroups', 'ownGroups'));
    }

    /**
     * Update the specified campaign
     */
    public function update(Request $request, MessageCampaign $campaign)
    {
        $validator = Validator::make($request->all(), [
            'campaign_name' => 'required|string|max:255',
            'target_group_id' => 'required|exists:target_groups,id',
            'own_group_id' => 'nullable|exists:target_groups,id',
            'message_template' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'daily_target' => 'required|integer|min:1|max:500',
            'status' => 'required|in:draft,active,paused,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $campaign->update($request->all());

        return redirect()->route('admin.zalo.campaigns.index')
            ->with('success', 'Chiến dịch đã được cập nhật!');
    }

    /**
     * Remove the specified campaign
     */
    public function destroy(MessageCampaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('admin.zalo.campaigns.index')
            ->with('success', 'Chiến dịch đã được xóa!');
    }

    /**
     * Update campaign statistics
     */
    public function updateStats(MessageCampaign $campaign)
    {
        $campaign->updateStatistics();

        return redirect()->back()
            ->with('success', 'Thống kê đã được cập nhật!');
    }

    /**
     * Get daily statistics for campaign
     */
    private function getDailyStatistics(MessageCampaign $campaign)
    {
        $stats = $campaign->messageLogs()
            ->selectRaw('DATE(sent_at) as date, 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered,
                        SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            ->whereNotNull('sent_at')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return $stats;
    }

    /**
     * Show campaign report
     */
    public function report(MessageCampaign $campaign)
    {
        $campaign->load(['targetGroup', 'ownGroup']);

        // Daily statistics
        $dailyStats = $this->getDailyStatistics($campaign);

        // Conversion statistics
        $conversionStats = $campaign->conversions()
            ->selectRaw('DATE(joined_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Average days to convert
        $avgDaysToConvert = $campaign->conversions()
            ->whereNotNull('days_to_convert')
            ->avg('days_to_convert');

        return view('zalo.campaigns.report', compact(
            'campaign',
            'dailyStats',
            'conversionStats',
            'avgDaysToConvert'
        ));
    }
}
