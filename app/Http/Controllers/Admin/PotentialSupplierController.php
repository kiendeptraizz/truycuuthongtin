<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PotentialSupplier;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PotentialSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = PotentialSupplier::with('services');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                    ->orWhere('supplier_code', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhereHas('services', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('service_name', 'like', "%{$search}%");
                    });
            });
        }

        // Lọc theo dịch vụ
        if ($request->filled('service_filter')) {
            $serviceFilter = $request->get('service_filter');
            $query->whereHas('services', function ($serviceQuery) use ($serviceFilter) {
                $serviceQuery->where('service_name', 'like', "%{$serviceFilter}%");
            });
        }

        // Lọc theo mức độ ưu tiên
        if ($request->filled('priority_filter')) {
            $query->where('priority', $request->get('priority_filter'));
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $potentialSuppliers = $query->paginate(20);

        // Thống kê
        $stats = [
            'total' => PotentialSupplier::count(),
            'total_estimated_value' => \App\Models\PotentialSupplierService::sum('estimated_price'),
            'high_priority' => PotentialSupplier::where('priority', 'high')->count(),
            'medium_priority' => PotentialSupplier::where('priority', 'medium')->count(),
            'low_priority' => PotentialSupplier::where('priority', 'low')->count(),
        ];

        return view('admin.potential-suppliers.index', compact('potentialSuppliers', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.potential-suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'reason_potential' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'expected_cooperation_date' => 'nullable|date',
            'services' => 'required|array|min:1',
            'services.*.service_name' => 'required|string|max:255',
            'services.*.estimated_price' => 'required|numeric|min:0',
            'services.*.description' => 'nullable|string',
            'services.*.unit' => 'nullable|string|max:50',
            'services.*.warranty_days' => 'nullable|integer|min:0|max:3650',
            'services.*.notes' => 'nullable|string',
        ]);

        // Tạo potential supplier
        $potentialSupplier = PotentialSupplier::create([
            'supplier_name' => $validated['supplier_name'],
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'website' => $validated['website'],
            'notes' => $validated['notes'],
            'reason_potential' => $validated['reason_potential'],
            'priority' => $validated['priority'],
            'expected_cooperation_date' => $validated['expected_cooperation_date'],
        ]);

        // Tạo các dịch vụ
        foreach ($validated['services'] as $serviceData) {
            $potentialSupplier->services()->create([
                'service_name' => $serviceData['service_name'],
                'estimated_price' => $serviceData['estimated_price'],
                'description' => $serviceData['description'] ?? null,
                'unit' => $serviceData['unit'] ?? null,
                'warranty_days' => $serviceData['warranty_days'] ?? 0,
                'notes' => $serviceData['notes'] ?? null,
            ]);
        }

        return redirect()->route('admin.potential-suppliers.index')
            ->with('success', 'Nhà cung cấp tiềm năng đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PotentialSupplier $potentialSupplier): View
    {
        $potentialSupplier->load('services');
        return view('admin.potential-suppliers.show', compact('potentialSupplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PotentialSupplier $potentialSupplier): View
    {
        $potentialSupplier->load('services');
        return view('admin.potential-suppliers.edit', compact('potentialSupplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PotentialSupplier $potentialSupplier): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'reason_potential' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'expected_cooperation_date' => 'nullable|date',
            'services' => 'required|array|min:1',
            'services.*.service_name' => 'required|string|max:255',
            'services.*.estimated_price' => 'required|numeric|min:0',
            'services.*.description' => 'nullable|string',
            'services.*.unit' => 'nullable|string|max:50',
            'services.*.warranty_days' => 'nullable|integer|min:0|max:3650',
            'services.*.notes' => 'nullable|string',
            'services.*.id' => 'nullable|exists:potential_supplier_services,id',
        ]);

        // Cập nhật thông tin potential supplier
        $potentialSupplier->update([
            'supplier_name' => $validated['supplier_name'],
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'website' => $validated['website'],
            'notes' => $validated['notes'],
            'reason_potential' => $validated['reason_potential'],
            'priority' => $validated['priority'],
            'expected_cooperation_date' => $validated['expected_cooperation_date'],
        ]);

        // Lấy danh sách ID dịch vụ hiện có
        $existingServiceIds = collect($validated['services'])
            ->pluck('id')
            ->filter()
            ->toArray();

        // Xóa các dịch vụ không còn trong danh sách
        $potentialSupplier->services()
            ->whereNotIn('id', $existingServiceIds)
            ->delete();

        // Cập nhật hoặc tạo mới các dịch vụ
        foreach ($validated['services'] as $serviceData) {
            if (isset($serviceData['id'])) {
                // Cập nhật dịch vụ hiện có
                $potentialSupplier->services()
                    ->where('id', $serviceData['id'])
                    ->update([
                        'service_name' => $serviceData['service_name'],
                        'estimated_price' => $serviceData['estimated_price'],
                        'description' => $serviceData['description'] ?? null,
                        'unit' => $serviceData['unit'] ?? null,
                        'warranty_days' => $serviceData['warranty_days'] ?? 0,
                        'notes' => $serviceData['notes'] ?? null,
                    ]);
            } else {
                // Tạo dịch vụ mới
                $potentialSupplier->services()->create([
                    'service_name' => $serviceData['service_name'],
                    'estimated_price' => $serviceData['estimated_price'],
                    'description' => $serviceData['description'] ?? null,
                    'unit' => $serviceData['unit'] ?? null,
                    'warranty_days' => $serviceData['warranty_days'] ?? 0,
                    'notes' => $serviceData['notes'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.potential-suppliers.show', $potentialSupplier)
            ->with('success', 'Thông tin nhà cung cấp tiềm năng đã được cập nhật!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PotentialSupplier $potentialSupplier): RedirectResponse
    {
        $potentialSupplier->delete();

        return redirect()->route('admin.potential-suppliers.index')
            ->with('success', 'Nhà cung cấp tiềm năng đã được xóa thành công!');
    }

    /**
     * Convert potential supplier to actual supplier
     */
    public function convertToSupplier(PotentialSupplier $potentialSupplier): RedirectResponse
    {
        // Tạo supplier mới
        $supplier = Supplier::create([
            'supplier_name' => $potentialSupplier->supplier_name,
        ]);

        // Chuyển đổi các dịch vụ
        foreach ($potentialSupplier->services as $service) {
            $supplier->products()->create([
                'product_name' => $service->service_name,
                'price' => $service->estimated_price,
                'warranty_days' => $service->warranty_days ?? 0,
            ]);
        }

        // Xóa potential supplier
        $potentialSupplier->delete();

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', 'Nhà cung cấp tiềm năng đã được chuyển đổi thành nhà cung cấp chính thức!');
    }

    /**
     * Bulk delete potential suppliers
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:potential_suppliers,id'
        ]);

        PotentialSupplier::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.potential-suppliers.index')
            ->with('success', 'Đã xóa ' . count($request->ids) . ' nhà cung cấp tiềm năng!');
    }

    /**
     * API endpoint for potential suppliers data
     */
    public function apiPotentialSuppliers(Request $request)
    {
        $query = PotentialSupplier::with('services');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                    ->orWhere('supplier_code', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhereHas('services', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('service_name', 'like', "%{$search}%");
                    });
            });
        }

        // Lọc theo dịch vụ
        if ($request->filled('service_filter')) {
            $serviceFilter = $request->get('service_filter');
            $query->whereHas('services', function ($serviceQuery) use ($serviceFilter) {
                $serviceQuery->where('service_name', 'like', "%{$serviceFilter}%");
            });
        }

        // Lọc theo mức độ ưu tiên
        if ($request->filled('priority_filter')) {
            $query->where('priority', $request->get('priority_filter'));
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $potentialSuppliers = $query->paginate(20);

        // Thống kê
        $stats = [
            'total' => PotentialSupplier::count(),
            'total_estimated_value' => \App\Models\PotentialSupplierService::sum('estimated_price'),
            'high_priority' => PotentialSupplier::where('priority', 'high')->count(),
            'medium_priority' => PotentialSupplier::where('priority', 'medium')->count(),
            'low_priority' => PotentialSupplier::where('priority', 'low')->count(),
        ];

        return response()->json([
            'potentialSuppliers' => $potentialSuppliers,
            'stats' => $stats
        ]);
    }
}
