<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = ServiceCategory::withCount('servicePackages');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name')->paginate(15);

        return view('admin.service-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.service-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        ServiceCategory::create($request->only(['name', 'description']));

        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Danh mục dịch vụ đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceCategory $serviceCategory): View
    {
        $serviceCategory->load(['servicePackages' => function ($query) {
            $query->withCount('customerServices')->orderBy('name');
        }]);

        return view('admin.service-categories.show', compact('serviceCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCategory $serviceCategory): View
    {
        return view('admin.service-categories.edit', compact('serviceCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name,' . $serviceCategory->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $serviceCategory->update($request->only(['name', 'description']));

        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Danh mục dịch vụ đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCategory $serviceCategory): RedirectResponse
    {
        // Check if category has service packages
        if ($serviceCategory->servicePackages()->count() > 0) {
            return redirect()->route('admin.service-categories.index')
                ->with('error', 'Không thể xóa danh mục đang có gói dịch vụ!');
        }

        $serviceCategory->delete();

        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Danh mục dịch vụ đã được xóa thành công!');
    }

    /**
     * Get categories for AJAX requests
     */
    public function getCategories(): \Illuminate\Http\JsonResponse
    {
        $categories = ServiceCategory::orderBy('name')->get(['id', 'name']);
        
        return response()->json($categories);
    }
}
