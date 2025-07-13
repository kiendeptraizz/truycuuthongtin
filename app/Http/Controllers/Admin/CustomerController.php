<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('customerServices.servicePackage.category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%");
            });
        }

        // Filter by service package
        if ($request->filled('service_package_id')) {
            $query->whereHas('customerServices', function ($q) use ($request) {
                $q->where('service_package_id', $request->service_package_id);
            });
        }

        // Filter by service status
        if ($request->filled('service_status')) {
            $status = $request->service_status;
            $query->whereHas('customerServices', function ($q) use ($status) {
                if ($status === 'active') {
                    $q->where('status', 'active');
                } elseif ($status === 'expired') {
                    $q->where('expires_at', '<', now());
                } elseif ($status === 'cancelled') {
                    $q->where('status', 'cancelled');
                }
            });
        }

        // Filter by login email
        if ($request->filled('login_email')) {
            $query->whereHas('customerServices', function ($q) use ($request) {
                $q->where('login_email', $request->login_email);
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);
        $servicePackages = \App\Models\ServicePackage::all();

        // Get unique login emails for filter dropdown
        $loginEmails = \App\Models\CustomerService::select('login_email')
            ->whereNotNull('login_email')
            ->where('login_email', '!=', '')
            ->distinct()
            ->orderBy('login_email')
            ->pluck('login_email');

        return view('admin.customers.index', compact('customers', 'servicePackages', 'loginEmails'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Customer::create($request->all());

        return redirect()->route('admin.customers.index')
            ->with('success', 'Khách hàng đã được tạo thành công!');
    }

    public function show(Customer $customer)
    {
        $customer->load(['customerServices.servicePackage.category', 'customerServices.assignedBy']);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer->update($request->only(['name', 'email', 'phone']));

        return redirect()->route('admin.customers.index')
            ->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Xóa thành công!');
    }
}
