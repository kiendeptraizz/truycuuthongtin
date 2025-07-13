<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ServicePackage;
use App\Models\CustomerService;
use App\Models\ContentPost;
use App\Models\Lead;

class DashboardController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $totalCustomers = Customer::count();
        $totalServicePackages = ServicePackage::count();
        $totalActiveServices = CustomerService::where('status', 'active')->count();
        $expiringSoonServices = CustomerService::expiringSoon()->count();

        // Lead statistics
        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'new')->count();
        $followUpTodayLeads = Lead::needFollowUpToday()->count();
        $overdueLeads = Lead::overdueFollowUp()->count();
        $convertedThisMonth = Lead::where('status', 'won')
            ->whereMonth('converted_at', now()->month)
            ->count();

        // Leads cần chú ý
        $urgentLeads = Lead::with(['servicePackage', 'assignedUser'])
            ->where('priority', 'urgent')
            ->whereNotIn('status', ['won', 'lost'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $overdueLeadsList = Lead::with(['servicePackage', 'assignedUser'])
            ->overdueFollowUp()
            ->orderBy('next_follow_up_at')
            ->limit(5)
            ->get();

        // Dịch vụ sắp hết hạn (7 ngày tới)
        $expiringSoon = CustomerService::with(['customer', 'servicePackage'])
            ->expiringSoon()
            ->orderBy('expires_at')
            ->limit(10)
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

        return view('admin.dashboard', compact(
            'totalCustomers',
            'totalServicePackages',
            'totalActiveServices',
            'expiringSoonServices',
            'expiringSoon',
            'recentCustomers',
            'popularServices',
            'upcomingPosts',
            'overduePosts',
            'totalLeads',
            'newLeads',
            'followUpTodayLeads',
            'overdueLeads',
            'convertedThisMonth',
            'urgentLeads',
            'overdueLeadsList'
        ));
    }
}
