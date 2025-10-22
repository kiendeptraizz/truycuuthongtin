<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ServicePackage;
use App\Models\CustomerService;
use App\Models\ContentPost;

class DashboardController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $totalCustomers = Customer::count();
        $totalServicePackages = ServicePackage::count();
        $totalActiveServices = CustomerService::where('status', 'active')->count();
        $expiringSoonServices = CustomerService::expiringSoon()->count();



        // Dịch vụ sắp hết hạn (5 ngày tới)
        $expiringSoon = CustomerService::with(['customer', 'servicePackage'])
            ->expiringSoon()
            ->orderBy('expires_at')
            ->limit(10)
            ->get();

        // Dịch vụ đã hết hạn
        $expiredServices = CustomerService::with(['customer', 'servicePackage'])
            ->where('expires_at', '<', now())
            ->where('status', 'active') // Chỉ lấy những dịch vụ đang active nhưng đã hết hạn
            ->orderBy('expires_at', 'desc')
            ->limit(15)
            ->get();

        // Khách hàng mới nhất
        $recentCustomers = Customer::with('customerServices')
            ->latest()
            ->limit(5)
            ->get();

        // Dịch vụ phổ biến nhất
        $popularServices = ServicePackage::withCount('customerServices')
            ->orderBy('customer_services_count', 'desc')
            ->limit(5)
            ->get();

        // Content posts cần chú ý
        $upcomingPosts = ContentPost::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now()->addHours(24))
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $overduePosts = ContentPost::overdue()->limit(5)->get();

        // Thống kê gán dịch vụ
        $recentAssignments = CustomerService::with(['customer', 'servicePackage'])
            ->latest()
            ->limit(3)
            ->get();

        $assignmentsToday = CustomerService::whereDate('created_at', today())->count();
        $assignmentsThisWeek = CustomerService::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        return view('admin.dashboard', compact(
            'totalCustomers',
            'totalServicePackages',
            'totalActiveServices',
            'expiringSoonServices',
            'expiringSoon',
            'expiredServices',
            'recentCustomers',
            'popularServices',
            'upcomingPosts',
            'overduePosts',
            'recentAssignments',
            'assignmentsToday',
            'assignmentsThisWeek'
        ));
    }
}
