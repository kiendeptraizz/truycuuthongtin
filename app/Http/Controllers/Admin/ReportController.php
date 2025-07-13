<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function profit(Request $request)
    {
        // Lấy tham số thời gian
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Thống kê dịch vụ đã bán trong khoảng thời gian
        $soldServices = CustomerService::with(['servicePackage', 'customer'])
            ->whereBetween('activated_at', [$startDate, $endDate])
            ->get();

        // Tính tổng doanh thu và lợi nhuận
        $totalRevenue = $soldServices->sum(function ($service) {
            return $service->servicePackage->price;
        });

        $totalCost = $soldServices->sum(function ($service) {
            return $service->servicePackage->cost_price ?? 0;
        });

        $totalProfit = $totalRevenue - $totalCost;

        // Thống kê theo gói dịch vụ
        $packageStats = $soldServices->groupBy('service_package_id')->map(function ($services) {
            $package = $services->first()->servicePackage;
            $count = $services->count();
            $revenue = $count * $package->price;
            $cost = $count * ($package->cost_price ?? 0);
            $profit = $revenue - $cost;

            return [
                'package' => $package,
                'count' => $count,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
                'profit_margin' => $package->cost_price ? round(($profit / $cost) * 100, 2) : 0
            ];
        })->sortByDesc('profit');

        // Thống kê theo tháng (6 tháng gần nhất)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthServices = CustomerService::with('servicePackage')
                ->whereYear('activated_at', $month->year)
                ->whereMonth('activated_at', $month->month)
                ->get();

            $monthRevenue = $monthServices->sum(function ($service) {
                return $service->servicePackage->price;
            });

            $monthCost = $monthServices->sum(function ($service) {
                return $service->servicePackage->cost_price ?? 0;
            });

            $monthlyStats[] = [
                'month' => $month->format('m/Y'),
                'count' => $monthServices->count(),
                'revenue' => $monthRevenue,
                'cost' => $monthCost,
                'profit' => $monthRevenue - $monthCost
            ];
        }

        return view('admin.reports.profit', compact(
            'soldServices',
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'packageStats',
            'monthlyStats',
            'startDate',
            'endDate'
        ));
    }
}
