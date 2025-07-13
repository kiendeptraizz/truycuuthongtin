<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\CollaboratorService;
use App\Models\CollaboratorServiceAccount;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CollaboratorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Collaborator::with(['services']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('collaborator_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter theo status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $collaborators = $query->paginate(20);

        // Thống kê
        $stats = [
            'total' => Collaborator::count(),
            'active' => Collaborator::where('status', 'active')->count(),
            'inactive' => Collaborator::where('status', 'inactive')->count(),
            'total_services' => CollaboratorService::count(),
            'total_accounts' => CollaboratorServiceAccount::count(),
        ];

        return view('admin.collaborators.index', compact('collaborators', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.collaborators.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*.service_name' => 'required_with:services|string|max:255',
            'services.*.price' => 'required_with:services|numeric|min:0',
            'services.*.quantity' => 'required_with:services|integer|min:1',
            'services.*.warranty_period' => 'nullable|integer|min:0',
            'services.*.description' => 'nullable|string',
        ]);

        // Tạo collaborator
        $collaborator = Collaborator::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Tạo các dịch vụ nếu có
        if (!empty($validated['services'])) {
            foreach ($validated['services'] as $serviceData) {
                $collaborator->services()->create([
                    'service_name' => $serviceData['service_name'],
                    'price' => $serviceData['price'],
                    'quantity' => $serviceData['quantity'],
                    'warranty_period' => $serviceData['warranty_period'] ?? 0,
                    'description' => $serviceData['description'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.collaborators.index')
            ->with('success', 'Cộng tác viên đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Collaborator $collaborator): View
    {
        $collaborator->load(['services.accounts']);

        return view('admin.collaborators.show', compact('collaborator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collaborator $collaborator): View
    {
        $collaborator->load('services');
        return view('admin.collaborators.edit', compact('collaborator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collaborator $collaborator): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*.service_name' => 'required_with:services|string|max:255',
            'services.*.price' => 'required_with:services|numeric|min:0',
            'services.*.quantity' => 'required_with:services|integer|min:1',
            'services.*.warranty_period' => 'nullable|integer|min:0',
            'services.*.description' => 'nullable|string',
            'services.*.id' => 'nullable|exists:collaborator_services,id',
        ]);

        // Cập nhật thông tin collaborator
        $collaborator->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Xử lý services
        if (isset($validated['services'])) {
            // Lấy danh sách ID services được gửi lên
            $sentServiceIds = collect($validated['services'])
                ->filter(function ($service) {
                    return isset($service['id']);
                })
                ->pluck('id')
                ->toArray();

            // Xóa các services không còn trong danh sách
            $collaborator->services()
                ->whereNotIn('id', $sentServiceIds)
                ->delete();

            // Cập nhật hoặc tạo mới các services
            foreach ($validated['services'] as $serviceData) {
                if (isset($serviceData['id'])) {
                    // Cập nhật service hiện có
                    $collaborator->services()
                        ->where('id', $serviceData['id'])
                        ->update([
                            'service_name' => $serviceData['service_name'],
                            'price' => $serviceData['price'],
                            'quantity' => $serviceData['quantity'],
                            'warranty_period' => $serviceData['warranty_period'] ?? 0,
                            'description' => $serviceData['description'] ?? null,
                        ]);
                } else {
                    // Tạo service mới
                    $collaborator->services()->create([
                        'service_name' => $serviceData['service_name'],
                        'price' => $serviceData['price'],
                        'quantity' => $serviceData['quantity'],
                        'warranty_period' => $serviceData['warranty_period'] ?? 0,
                        'description' => $serviceData['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.collaborators.show', $collaborator)
            ->with('success', 'Thông tin cộng tác viên đã được cập nhật!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collaborator $collaborator): RedirectResponse
    {
        $collaborator->delete();

        return redirect()->route('admin.collaborators.index')
            ->with('success', 'Cộng tác viên đã được xóa!');
    }

    /**
     * Store a new account for a specific collaborator service
     */
    public function storeAccount(Request $request, Collaborator $collaborator, CollaboratorService $service)
    {
        // Validate the request
        $validated = $request->validate([
            'account_info' => 'required|string',
            'provided_date' => 'required|date',
            'expiry_date' => 'required|date|after:provided_date',
            'status' => 'required|in:active,expired,disabled',
            'notes' => 'nullable|string',
        ]);

        // Verify that the service belongs to the collaborator
        if ($service->collaborator_id !== $collaborator->id) {
            return response()->json(['error' => 'Service không thuộc về collaborator này'], 400);
        }

        // Create the account
        $account = $service->accounts()->create([
            'account_info' => $validated['account_info'],
            'provided_date' => $validated['provided_date'],
            'expiry_date' => $validated['expiry_date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tài khoản đã được thêm thành công!',
                'account' => $account
            ]);
        }

        return redirect()->route('admin.collaborators.show', $collaborator)
            ->with('success', 'Tài khoản đã được thêm thành công!');
    }
}
