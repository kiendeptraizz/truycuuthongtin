<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServicePackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ServicePackage::with(['category', 'customerServices']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('account_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $servicePackages = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = ServiceCategory::orderBy('name')->get();

        return view('admin.service-packages.index', compact('servicePackages', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ServiceCategory::orderBy('name')->get();

        return view('admin.service-packages.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'account_type' => 'required|string|max:255',
            'default_duration_days' => 'required|integer|min:1',
            'price' => 'required|string',
            'cost_price' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Parse currency inputs
        $data = $request->all();
        $data['price'] = parseCurrency($request->price);
        $data['cost_price'] = $request->cost_price ? parseCurrency($request->cost_price) : null;

        ServicePackage::create($data);

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Gói dịch vụ đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServicePackage $servicePackage)
    {
        $servicePackage->load(['category', 'customerServices.customer']);

        return view('admin.service-packages.show', compact('servicePackage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServicePackage $servicePackage)
    {
        $categories = ServiceCategory::orderBy('name')->get();

        return view('admin.service-packages.edit', compact('servicePackage', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServicePackage $servicePackage)
    {
        $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'account_type' => 'required|string|max:255',
            'default_duration_days' => 'required|integer|min:1',
            'price' => 'required|string',
            'cost_price' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Parse currency inputs
        $data = $request->all();
        $data['price'] = parseCurrency($request->price);
        $data['cost_price'] = $request->cost_price ? parseCurrency($request->cost_price) : null;

        $servicePackage->update($data);

        return redirect(route('admin.service-packages.index') . '#package-' . $servicePackage->id)
            ->with('success', 'Gói dịch vụ đã được cập nhật!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServicePackage $servicePackage)
    {
        // Check if package has active services
        if ($servicePackage->customerServices()->where('status', 'active')->exists()) {
            return redirect()->route('admin.service-packages.index')
                ->with('error', 'Không thể xóa gói dịch vụ đang có khách hàng sử dụng!');
        }

        $servicePackage->delete();

        return redirect()->route('admin.service-packages.index')
            ->with('success', 'Gói dịch vụ đã được xóa!');
    }

    /**
     * Toggle the status of the service package.
     */
    public function toggleStatus(ServicePackage $servicePackage)
    {
        $servicePackage->update([
            'is_active' => !$servicePackage->is_active
        ]);

        $status = $servicePackage->is_active ? 'kích hoạt' : 'tạm dừng';

        return redirect(route('admin.service-packages.index') . '#package-' . $servicePackage->id)
            ->with('success', "Gói dịch vụ đã được {$status}!");
    }
}
