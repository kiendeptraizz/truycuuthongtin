<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Supplier::with('products')
            ->withCount('products as product_count')
            ->addSelect([
                'total_value' => \App\Models\SupplierProduct::selectRaw('SUM(price)')
                    ->whereColumn('supplier_id', 'suppliers.id')
            ]);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                    ->orWhere('supplier_code', 'like', "%{$search}%")
                    ->orWhereHas('products', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $suppliers = $query->paginate(20);

        // Thống kê
        $stats = [
            'total' => Supplier::count(),
            'total_value' => \App\Models\SupplierProduct::sum('price'),
            'active' => 0,
            'inactive' => 0,
            'over_credit' => 0,
        ];

        return view('admin.suppliers.index', compact('suppliers', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_name' => 'required|string|max:255',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.warranty_days' => 'nullable|integer|min:0|max:3650',
        ]);

        // Tạo supplier
        $supplier = Supplier::create([
            'supplier_name' => $validated['supplier_name']
        ]);

        // Tạo các sản phẩm
        foreach ($validated['products'] as $productData) {
            $supplier->products()->create([
                'product_name' => $productData['product_name'],
                'price' => $productData['price'],
                'warranty_days' => $productData['warranty_days'] ?? 0,
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Nhà cung cấp và dịch vụ đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): View
    {
        $supplier->load('products');
        return view('admin.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): View
    {
        $supplier->load('products');
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_name' => 'required|string|max:255',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.warranty_days' => 'nullable|integer|min:0|max:3650',
            'products.*.id' => 'nullable|exists:supplier_products,id',
        ]);

        // Cập nhật tên nhà cung cấp
        $supplier->update([
            'supplier_name' => $validated['supplier_name']
        ]);

        // Lấy danh sách ID sản phẩm được gửi lên
        $sentProductIds = collect($validated['products'])
            ->filter(function ($product) {
                return isset($product['id']);
            })
            ->pluck('id')
            ->toArray();

        // Xóa các sản phẩm không còn trong danh sách
        $supplier->products()
            ->whereNotIn('id', $sentProductIds)
            ->delete();

        // Cập nhật hoặc tạo mới các sản phẩm
        foreach ($validated['products'] as $productData) {
            if (isset($productData['id'])) {
                // Cập nhật sản phẩm hiện có
                $supplier->products()
                    ->where('id', $productData['id'])
                    ->update([
                        'product_name' => $productData['product_name'],
                        'price' => $productData['price'],
                        'warranty_days' => $productData['warranty_days'] ?? 0,
                    ]);
            } else {
                // Tạo sản phẩm mới
                $supplier->products()->create([
                    'product_name' => $productData['product_name'],
                    'price' => $productData['price'],
                    'warranty_days' => $productData['warranty_days'] ?? 0,
                ]);
            }
        }

        return redirect()->route('admin.suppliers.show', $supplier)
            ->with('success', 'Thông tin nhà cung cấp và dịch vụ đã được cập nhật!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Nhà cung cấp đã được xóa!');
    }

    /**
     * Remove multiple suppliers from storage.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_ids' => 'required|string'
        ]);

        $supplierIds = explode(',', $request->supplier_ids);
        $supplierIds = array_filter($supplierIds); // Remove empty values

        if (empty($supplierIds)) {
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Không có nhà cung cấp nào được chọn để xóa!');
        }

        $deletedCount = Supplier::whereIn('id', $supplierIds)->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', "Đã xóa thành công {$deletedCount} nhà cung cấp!");
    }
}
