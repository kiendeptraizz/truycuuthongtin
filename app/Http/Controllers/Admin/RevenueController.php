<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\CustomerService;
use App\Models\Profit;
use App\Models\ServicePackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    /**
     * Hiển thị trang thống kê doanh thu
     */
    public function index(Request $request): View
    {
        // Lấy ngày hiện tại
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Thống kê tổng quan
        $stats = $this->getGeneralStats();

        return view('admin.revenue.index', compact('stats', 'today'));
    }

    /**
     * Lấy thống kê tổng quan
     */
    private function getGeneralStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        return [
            // Doanh thu hôm nay
            'today_revenue' => CustomerService::whereDate('customer_services.created_at', $today)
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'today_orders' => CustomerService::whereDate('customer_services.created_at', $today)->count(),

            // Doanh thu tháng này
            'month_revenue' => CustomerService::where('customer_services.created_at', '>=', $thisMonth)
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'month_orders' => CustomerService::where('customer_services.created_at', '>=', $thisMonth)->count(),

            // Doanh thu năm này
            'year_revenue' => CustomerService::where('customer_services.created_at', '>=', $thisYear)
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'year_orders' => CustomerService::where('customer_services.created_at', '>=', $thisYear)->count(),

            // Lợi nhuận
            'total_profit' => Profit::sum('profit_amount'),
            'month_profit' => Profit::where('profits.created_at', '>=', $thisMonth)->sum('profit_amount'),
            'today_profit' => Profit::whereDate('profits.created_at', $today)->sum('profit_amount'),
        ];
    }

    /**
     * Lấy dữ liệu doanh thu theo khoảng thời gian
     */
    public function getRevenueData(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $groupBy = $request->get('group_by', 'day'); // day, month, year

        // Validate dates
        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Base query
        $query = CustomerService::with(['customer', 'servicePackage', 'profit'])
            ->whereBetween('customer_services.created_at', [$start, $end]);

        // Get orders with revenue data
        $orders = $query->orderBy('customer_services.created_at', 'desc')->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'customer_name' => $order->customer ? $order->customer->name : '[Không có tên]',
                'customer_code' => $order->customer ? $order->customer->customer_code : '',
                'customer_display' => $order->customer
                    ? ($order->customer->customer_code
                        ? "{$order->customer->name} ({$order->customer->customer_code})"
                        : $order->customer->name)
                    : '[Khách hàng không tồn tại]',
                'service_name' => $order->servicePackage ? $order->servicePackage->name : '[Gói dịch vụ không tồn tại]',
                'revenue' => $order->servicePackage ? $order->servicePackage->price : 0,
                'profit' => $order->profit ? $order->profit->profit_amount : 0,
                'profit_notes' => $order->profit ? $order->profit->notes : '',
                'profit_margin' => $order->servicePackage && $order->profit
                    ? round(($order->profit->profit_amount / $order->servicePackage->price) * 100, 2)
                    : 0,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'status' => $order->status,
            ];
        });

        // Get chart data grouped by period
        $chartData = $this->getChartData($start, $end, $groupBy);

        // Summary statistics
        $summary = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('revenue'),
            'total_profit' => $orders->sum('profit'),
            'average_order_value' => $orders->count() > 0 ? round($orders->sum('revenue') / $orders->count(), 0) : 0,
            'profit_margin' => $orders->sum('revenue') > 0
                ? round(($orders->sum('profit') / $orders->sum('revenue')) * 100, 2)
                : 0,
        ];

        return response()->json([
            'orders' => $orders,
            'chart_data' => $chartData,
            'summary' => $summary,
        ]);
    }

    /**
     * Lấy dữ liệu biểu đồ theo khoảng thời gian
     */
    private function getChartData($start, $end, $groupBy)
    {
        $format = match ($groupBy) {
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m-%d',
        };

        $dateFormat = match ($groupBy) {
            'month' => 'Y-m',
            'year' => 'Y',
            default => 'Y-m-d',
        };

        $data = CustomerService::select(
            DB::raw("DATE_FORMAT(customer_services.created_at, '$format') as period"),
            DB::raw('COUNT(*) as orders_count'),
            DB::raw('SUM(service_packages.price) as revenue'),
            DB::raw('COALESCE(SUM(profits.profit_amount), 0) as profit')
        )
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->leftJoin('profits', 'customer_services.id', '=', 'profits.customer_service_id')
            ->whereBetween('customer_services.created_at', [$start, $end])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Fill missing periods with zero values
        $periods = [];
        $current = $start->copy();

        while ($current <= $end) {
            $periodKey = $current->format($dateFormat);
            $periods[$periodKey] = [
                'period' => $periodKey,
                'orders_count' => 0,
                'revenue' => 0,
                'profit' => 0,
            ];

            match ($groupBy) {
                'month' => $current->addMonth(),
                'year' => $current->addYear(),
                default => $current->addDay(),
            };
        }

        // Merge actual data with periods
        foreach ($data as $item) {
            $periods[$item->period] = [
                'period' => $item->period,
                'orders_count' => (int) $item->orders_count,
                'revenue' => (float) $item->revenue,
                'profit' => (float) $item->profit,
            ];
        }

        return array_values($periods);
    }

    /**
     * Xuất báo cáo doanh thu
     */
    public function exportReport(Request $request)
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality coming soon']);
    }

    /**
     * Lấy thống kê theo dịch vụ
     */
    public function getServiceStats(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $serviceStats = ServicePackage::select(
            'service_packages.id',
            'service_packages.name',
            'service_packages.price',
            DB::raw('COUNT(customer_services.id) as orders_count'),
            DB::raw('SUM(service_packages.price) as total_revenue'),
            DB::raw('COALESCE(SUM(profits.profit_amount), 0) as total_profit')
        )
            ->leftJoin('customer_services', function ($join) use ($start, $end) {
                $join->on('service_packages.id', '=', 'customer_services.service_package_id')
                    ->whereBetween('customer_services.created_at', [$start, $end]);
            })
            ->leftJoin('profits', 'customer_services.id', '=', 'profits.customer_service_id')
            ->groupBy('service_packages.id', 'service_packages.name', 'service_packages.price')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return response()->json($serviceStats);
    }

    /**
     * Cập nhật lợi nhuận cho đơn hàng
     */
    public function updateProfit(Request $request): JsonResponse
    {
        $request->validate([
            'customer_service_id' => 'required|exists:customer_services,id',
            'profit_amount' => 'required|numeric|min:0',
            'profit_notes' => 'nullable|string|max:1000',
        ]);

        $customerService = CustomerService::findOrFail($request->customer_service_id);

        // Kiểm tra xem đã có lợi nhuận chưa
        if ($customerService->profit) {
            // Cập nhật lợi nhuận hiện có
            $customerService->profit->update([
                'profit_amount' => $request->profit_amount,
                'notes' => $request->profit_notes,
            ]);
            $message = 'Lợi nhuận đã được cập nhật thành công!';
        } else {
            // Tạo mới lợi nhuận
            Profit::create([
                'customer_service_id' => $customerService->id,
                'profit_amount' => $request->profit_amount,
                'notes' => $request->profit_notes,
                'created_by' => auth('admin')->id(),
            ]);
            $message = 'Lợi nhuận đã được thêm thành công!';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Xóa lợi nhuận của đơn hàng
     */
    public function deleteProfit(Request $request): JsonResponse
    {
        $request->validate([
            'customer_service_id' => 'required|exists:customer_services,id',
        ]);

        $customerService = CustomerService::findOrFail($request->customer_service_id);

        if ($customerService->profit) {
            $customerService->profit->delete();
            return response()->json(['success' => true, 'message' => 'Lợi nhuận đã được xóa thành công!']);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy lợi nhuận để xóa!'], 404);
    }
}
