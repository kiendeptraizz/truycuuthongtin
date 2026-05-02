<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\HomeSettings;
use App\Models\ServicePackage;
use Illuminate\Http\Request;

class HomeSettingsController extends Controller
{
    public function edit()
    {
        $settings = HomeSettings::singleton();
        $real = [
            'customers' => Customer::count(),
            'services' => CustomerService::where('status', 'active')->count(),
            'packages' => ServicePackage::where('is_active', true)->count(),
        ];

        return view('admin.home-settings.edit', compact('settings', 'real'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'customers_override' => 'nullable|integer|min:0|max:99999999',
            'services_override' => 'nullable|integer|min:0|max:99999999',
            'packages_override' => 'nullable|integer|min:0|max:99999999',
        ]);

        $settings = HomeSettings::singleton();
        $settings->fill([
            'customers_override' => $validated['customers_override'] ?? null,
            'services_override' => $validated['services_override'] ?? null,
            'packages_override' => $validated['packages_override'] ?? null,
        ])->save();

        return redirect()
            ->route('admin.home-settings.edit')
            ->with('success', 'Đã cập nhật cấu hình trang chủ. Số mới sẽ hiển thị ngay (cache đã clear).');
    }
}
