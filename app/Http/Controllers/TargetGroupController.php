<?php

namespace App\Http\Controllers;

use App\Models\TargetGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TargetGroupController extends Controller
{
    /**
     * Display a listing of target groups
     */
    public function index(Request $request)
    {
        $query = TargetGroup::query();

        if ($request->filled('group_type')) {
            $query->where('group_type', $request->group_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $groups = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('zalo.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new group
     */
    public function create()
    {
        return view('zalo.groups.create');
    }

    /**
     * Store a newly created group
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255',
            'group_link' => 'required|url',
            'group_id' => 'nullable|string',
            'topic' => 'nullable|string|max:255',
            'total_members' => 'nullable|integer|min:0',
            'opening_date' => 'nullable|date',
            'group_type' => 'required|in:competitor,own',
            'status' => 'required|in:active,inactive,completed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group = TargetGroup::create($request->all());

        return redirect()->route('admin.zalo.groups.index')
            ->with('success', 'Nhóm mục tiêu đã được tạo thành công!');
    }

    /**
     * Display the specified group
     */
    public function show(TargetGroup $group)
    {
        $group->load(['members' => function ($query) {
            $query->latest()->limit(100);
        }]);

        $stats = [
            'total_members' => $group->members()->count(),
            'new_members' => $group->newMembers()->count(),
            'contacted' => $group->contactedMembers()->count(),
            'converted' => $group->convertedMembers()->count(),
            'conversion_rate' => $this->calculateGroupConversionRate($group),
        ];

        return view('zalo.groups.show', compact('group', 'stats'));
    }

    /**
     * Show the form for editing the group
     */
    public function edit(TargetGroup $group)
    {
        return view('zalo.groups.edit', compact('group'));
    }

    /**
     * Update the specified group
     */
    public function update(Request $request, TargetGroup $group)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255',
            'group_link' => 'required|url',
            'group_id' => 'nullable|string',
            'topic' => 'nullable|string|max:255',
            'total_members' => 'nullable|integer|min:0',
            'opening_date' => 'nullable|date',
            'group_type' => 'required|in:competitor,own',
            'status' => 'required|in:active,inactive,completed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group->update($request->all());

        return redirect()->route('admin.zalo.groups.index')
            ->with('success', 'Nhóm mục tiêu đã được cập nhật!');
    }

    /**
     * Remove the specified group
     */
    public function destroy(TargetGroup $group)
    {
        $group->delete();

        return redirect()->route('admin.zalo.groups.index')
            ->with('success', 'Nhóm mục tiêu đã được xóa!');
    }

    /**
     * Show members of a group
     */
    public function members(TargetGroup $group)
    {
        $members = $group->members()
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('zalo.groups.members', compact('group', 'members'));
    }

    /**
     * Calculate conversion rate for group
     */
    private function calculateGroupConversionRate(TargetGroup $group): float
    {
        $contacted = $group->contactedMembers()->count() + $group->convertedMembers()->count();

        if ($contacted === 0) {
            return 0;
        }

        $converted = $group->convertedMembers()->count();

        return round(($converted / $contacted) * 100, 2);
    }
}
