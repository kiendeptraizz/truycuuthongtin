<?php

namespace App\Http\Controllers;

use App\Models\ZaloAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZaloAccountController extends Controller
{
    /**
     * Display a listing of Zalo accounts
     */
    public function index()
    {
        $accounts = ZaloAccount::orderBy('created_at', 'desc')->paginate(15);
        return view('zalo.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new account
     */
    public function create()
    {
        return view('zalo.accounts.create');
    }

    /**
     * Store a newly created account
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_name' => 'required|string|max:255',
            'email_or_phone' => 'required|string|unique:zalo_accounts,email_or_phone',
            'password' => 'nullable|string',
            'access_token' => 'nullable|string',
            'daily_message_limit' => 'required|integer|min:1|max:1000',
            'status' => 'required|in:active,inactive,blocked,error',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $account = ZaloAccount::create($request->all());

        return redirect()->route('admin.zalo.accounts.index')
            ->with('success', 'Tài khoản Zalo đã được tạo thành công!');
    }

    /**
     * Display the specified account
     */
    public function show(ZaloAccount $account)
    {
        $account->load(['messageLogs' => function ($query) {
            $query->latest()->limit(50);
        }]);

        $stats = [
            'total_messages' => $account->messageLogs()->count(),
            'today_messages' => $account->todayMessageLogs()->count(),
            'success_rate' => $this->calculateSuccessRate($account),
        ];

        return view('zalo.accounts.show', compact('account', 'stats'));
    }

    /**
     * Show the form for editing the account
     */
    public function edit(ZaloAccount $account)
    {
        return view('zalo.accounts.edit', compact('account'));
    }

    /**
     * Update the specified account
     */
    public function update(Request $request, ZaloAccount $account)
    {
        $validator = Validator::make($request->all(), [
            'account_name' => 'required|string|max:255',
            'email_or_phone' => 'required|string|unique:zalo_accounts,email_or_phone,' . $account->id,
            'password' => 'nullable|string',
            'access_token' => 'nullable|string',
            'daily_message_limit' => 'required|integer|min:1|max:1000',
            'status' => 'required|in:active,inactive,blocked,error',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['password', 'access_token']);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        if ($request->filled('access_token')) {
            $data['access_token'] = $request->access_token;
        }

        $account->update($data);

        return redirect()->route('admin.zalo.accounts.index')
            ->with('success', 'Tài khoản Zalo đã được cập nhật!');
    }

    /**
     * Remove the specified account
     */
    public function destroy(ZaloAccount $account)
    {
        $account->delete();

        return redirect()->route('admin.zalo.accounts.index')
            ->with('success', 'Tài khoản Zalo đã được xóa!');
    }

    /**
     * Reset message counter for account
     */
    public function resetCounter(ZaloAccount $account)
    {
        $account->messages_sent_today = 0;
        $account->save();

        return redirect()->back()
            ->with('success', 'Đã reset bộ đếm tin nhắn!');
    }

    /**
     * Calculate success rate for account
     */
    private function calculateSuccessRate(ZaloAccount $account): float
    {
        $total = $account->messageLogs()->count();
        if ($total === 0) {
            return 0;
        }

        $successful = $account->messageLogs()
            ->whereIn('status', ['sent', 'delivered'])
            ->count();

        return round(($successful / $total) * 100, 2);
    }
}
