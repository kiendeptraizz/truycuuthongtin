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
            $customer = Customer::where('customer_code', $code)
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
}
