<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class LookupController extends Controller
{
    public function index(Request $request)
    {
        $customer = null;
        $services = collect();
        $code = $request->get('code');

        if ($code) {
            // Tìm theo mã khách hàng hoặc email
            $customer = Customer::where(function ($query) use ($code) {
                $query->where('customer_code', $code)
                    ->orWhere('email', $code);
            })
                ->with(['customerServices.servicePackage.category'])
                ->first();

            if ($customer) {
                $services = $customer->customerServices()
                    ->with('servicePackage.category')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('lookup.index', compact('customer', 'services', 'code'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255'
        ]);

        $code = trim($request->get('code'));

        // Tìm theo mã khách hàng, email khách hàng, hoặc số điện thoại
        $customer = Customer::where(function ($query) use ($code) {
            $query->where('customer_code', $code)
                ->orWhere('email', $code)
                ->orWhere('phone', $code);
        })
            ->with(['customerServices.servicePackage.category'])
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin. Vui lòng kiểm tra lại mã khách hàng, email hoặc số điện thoại.'
            ], 404);
        }

        $services = $customer->customerServices()
            ->with('servicePackage.category')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'customer_code' => $customer->customer_code,
                    'created_at' => $customer->created_at->format('d/m/Y'),
                ],
                'services' => $services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'package_name' => $service->servicePackage->name ?? 'N/A',
                        'category_name' => $service->servicePackage->category->name ?? 'N/A',
                        'status' => $service->status,
                        'price' => $service->servicePackage->price ?? 0,
                        'activated_at' => $service->activated_at ? $service->activated_at->format('d/m/Y H:i') : null,
                        'expires_at' => $service->expires_at ? $service->expires_at->format('d/m/Y H:i') : null,
                    ];
                }),
            ]
        ]);
    }
}
