<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\CustomerService;
use App\Models\Profit;
use App\Models\ServicePackage;
use App\Models\Customer;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        return view('admin.revenue.index_advanced', compact('stats', 'today'));
    }

    /**
     * Lấy thống kê tổng quan
     */
    private function getGeneralStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $thisYear = Carbon::now()->startOfYear();
        $lastYear = Carbon::now()->subYear()->startOfYear();
        $lastYearEnd = Carbon::now()->subYear()->endOfYear();
        // Đảm bảo tuần bắt đầu từ thứ 2 (Monday) để đồng bộ với frontend
        $thisWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $lastWeek = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);

        return [
            // Doanh thu hôm nay
            'today_revenue' => CustomerService::whereDate('customer_services.created_at', $today)
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'today_orders' => CustomerService::whereDate('customer_services.created_at', $today)->count(),
            'today_profit' => Profit::whereDate('profits.created_at', $today)->sum('profit_amount'),

            // Doanh thu hôm qua (để so sánh)
            'yesterday_revenue' => CustomerService::whereDate('customer_services.created_at', $yesterday)
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'yesterday_orders' => CustomerService::whereDate('customer_services.created_at', $yesterday)->count(),
            'yesterday_profit' => Profit::whereDate('profits.created_at', $yesterday)->sum('profit_amount'),

            // Doanh thu tuần này
            'week_revenue' => CustomerService::where('customer_services.created_at', '>=', $thisWeek)
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'week_orders' => CustomerService::where('customer_services.created_at', '>=', $thisWeek)->count(),
            'week_profit' => Profit::where('profits.created_at', '>=', $thisWeek)->sum('profit_amount'),

            // Doanh thu tuần trước (để so sánh)
            'last_week_revenue' => CustomerService::whereBetween('customer_services.created_at', [$lastWeek, $lastWeekEnd])
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'last_week_orders' => CustomerService::whereBetween('customer_services.created_at', [$lastWeek, $lastWeekEnd])->count(),
            'last_week_profit' => Profit::whereBetween('profits.created_at', [$lastWeek, $lastWeekEnd])->sum('profit_amount'),

            // Doanh thu tháng này
            'month_revenue' => CustomerService::where('customer_services.created_at', '>=', $thisMonth)
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'month_orders' => CustomerService::where('customer_services.created_at', '>=', $thisMonth)->count(),
            'month_profit' => Profit::where('profits.created_at', '>=', $thisMonth)->sum('profit_amount'),

            // Doanh thu tháng trước (để so sánh)
            'last_month_revenue' => CustomerService::whereBetween('customer_services.created_at', [$lastMonth, $lastMonthEnd])
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'last_month_orders' => CustomerService::whereBetween('customer_services.created_at', [$lastMonth, $lastMonthEnd])->count(),
            'last_month_profit' => Profit::whereBetween('profits.created_at', [$lastMonth, $lastMonthEnd])->sum('profit_amount'),

            // Doanh thu năm này
            'year_revenue' => CustomerService::where('customer_services.created_at', '>=', $thisYear)
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'year_orders' => CustomerService::where('customer_services.created_at', '>=', $thisYear)->count(),
            'year_profit' => Profit::where('profits.created_at', '>=', $thisYear)->sum('profit_amount'),

            // Doanh thu năm trước (để so sánh)
            'last_year_revenue' => CustomerService::whereBetween('customer_services.created_at', [$lastYear, $lastYearEnd])
                ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
                ->sum('service_packages.price'),
            'last_year_orders' => CustomerService::whereBetween('customer_services.created_at', [$lastYear, $lastYearEnd])->count(),

            // Tổng lợi nhuán
            'total_profit' => Profit::sum('profit_amount'),

            // Thống kê khách hàng
            'total_customers' => Customer::count(),
            'new_customers_today' => Customer::whereDate('created_at', $today)->count(),
            'new_customers_month' => Customer::where('created_at', '>=', $thisMonth)->count(),

            // Thống kê chuyển đổi
            'conversion_rate_today' => $this->getConversionRate($today, $today),
            'conversion_rate_month' => $this->getConversionRate($thisMonth, $today),
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

            // Validate date range
            if ($start > $end) {
                return response()->json(['error' => 'Start date cannot be after end date'], 400);
            }
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
     * Tính tỷ lệ chuyển đổi từ khách hàng mới thành đơn hàng
     */
    private function getConversionRate($startDate, $endDate)
    {
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        $ordersFromNewCustomers = CustomerService::join('customers', 'customer_services.customer_id', '=', 'customers.id')
            ->whereBetween('customers.created_at', [$startDate, $endDate])
            ->whereBetween('customer_services.created_at', [$startDate, $endDate])
            ->count();

        return $newCustomers > 0 ? round(($ordersFromNewCustomers / $newCustomers) * 100, 2) : 0;
    }

    /**
     * Lấy thống kê theo khách hàng
     */
    public function getCustomerStats(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $limit = $request->get('limit', 10);

        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Top khách hàng theo doanh thu
        $topCustomers = DB::table('customer_services')
            ->join('customers', 'customer_services.customer_id', '=', 'customers.id')
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->leftJoin('profits', 'customer_services.id', '=', 'profits.customer_service_id')
            ->whereBetween('customer_services.created_at', [$start, $end])
            ->select(
                'customers.id',
                'customers.name',
                'customers.customer_code',
                'customers.phone',
                'customers.email',
                DB::raw('COUNT(customer_services.id) as total_orders'),
                DB::raw('SUM(service_packages.price) as total_revenue'),
                DB::raw('COALESCE(SUM(profits.profit_amount), 0) as total_profit'),
                DB::raw('AVG(service_packages.price) as avg_order_value')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.customer_code', 'customers.phone', 'customers.email')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($topCustomers);
    }

    /**
     * Lấy thống kê theo danh mục dịch vụ
     */
    public function getCategoryStats(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $categoryStats = DB::table('customer_services')
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->join('service_categories', 'service_packages.category_id', '=', 'service_categories.id')
            ->leftJoin('profits', 'customer_services.id', '=', 'profits.customer_service_id')
            ->whereBetween('customer_services.created_at', [$start, $end])
            ->select(
                'service_categories.id',
                'service_categories.name',
                'service_categories.description',
                DB::raw('COUNT(customer_services.id) as total_orders'),
                DB::raw('SUM(service_packages.price) as total_revenue'),
                DB::raw('COALESCE(SUM(profits.profit_amount), 0) as total_profit'),
                DB::raw('AVG(service_packages.price) as avg_order_value'),
                DB::raw('COUNT(DISTINCT service_packages.id) as services_count')
            )
            ->groupBy('service_categories.id', 'service_categories.name', 'service_categories.description')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return response()->json($categoryStats);
    }

    /**
     * Lấy thống kê hiệu suất theo thời gian
     */
    public function getPerformanceStats(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', Carbon::today()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $groupBy = $request->get('group_by', 'day');

        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $format = match ($groupBy) {
            'month' => '%Y-%m',
            'year' => '%Y',
            'week' => '%Y-%u',
            'hour' => '%Y-%m-%d %H:00:00',
            default => '%Y-%m-%d',
        };

        $performance = DB::table('customer_services')
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->leftJoin('profits', 'customer_services.id', '=', 'profits.customer_service_id')
            ->whereBetween('customer_services.created_at', [$start, $end])
            ->select(
                DB::raw("DATE_FORMAT(customer_services.created_at, '$format') as period"),
                DB::raw('COUNT(customer_services.id) as orders_count'),
                DB::raw('COUNT(DISTINCT customer_services.customer_id) as unique_customers'),
                DB::raw('SUM(service_packages.price) as total_revenue'),
                DB::raw('COALESCE(SUM(profits.profit_amount), 0) as total_profit'),
                DB::raw('AVG(service_packages.price) as avg_order_value'),
                DB::raw('MAX(service_packages.price) as max_order_value'),
                DB::raw('MIN(service_packages.price) as min_order_value')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($item) {
                $item->profit_margin = $item->total_revenue > 0
                    ? round(($item->total_profit / $item->total_revenue) * 100, 2)
                    : 0;
                $item->revenue_per_customer = $item->unique_customers > 0
                    ? round($item->total_revenue / $item->unique_customers, 0)
                    : 0;
                return $item;
            });

        return response()->json($performance);
    }

    /**
     * Lấy thống kê đặt hàng theo giờ trong ngày
     */
    public function getHourlyStats(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->toDateString());

        try {
            $targetDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $hourlyStats = DB::table('customer_services')
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->leftJoin('profits', 'customer_services.id', '=', 'profits.customer_service_id')
            ->whereDate('customer_services.created_at', $targetDate)
            ->select(
                DB::raw('HOUR(customer_services.created_at) as hour'),
                DB::raw('COUNT(customer_services.id) as orders_count'),
                DB::raw('SUM(service_packages.price) as total_revenue'),
                DB::raw('COALESCE(SUM(profits.profit_amount), 0) as total_profit')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Tạo array đầy đủ 24 giờ
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[$i] = [
                'hour' => $i,
                'orders_count' => 0,
                'total_revenue' => 0,
                'total_profit' => 0,
            ];
        }

        // Merge dữ liệu thực tế
        foreach ($hourlyStats as $stat) {
            $hours[$stat->hour] = [
                'hour' => $stat->hour,
                'orders_count' => (int) $stat->orders_count,
                'total_revenue' => (float) $stat->total_revenue,
                'total_profit' => (float) $stat->total_profit,
            ];
        }

        return response()->json(array_values($hours));
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

            // Validate date range
            if ($start > $end) {
                return response()->json(['error' => 'Start date cannot be after end date'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Lấy thống kê chính xác bằng cách tính từ customer_services
        $serviceStats = DB::table('customer_services')
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->leftJoin('profits', 'customer_services.id', '=', 'profits.customer_service_id')
            ->whereBetween('customer_services.created_at', [$start, $end])
            ->select(
                'service_packages.id',
                'service_packages.name',
                'service_packages.price',
                DB::raw('COUNT(customer_services.id) as orders_count'),
                DB::raw('COUNT(customer_services.id) * service_packages.price as total_revenue'),
                DB::raw('COALESCE(SUM(profits.profit_amount), 0) as total_profit')
            )
            ->groupBy('service_packages.id', 'service_packages.name', 'service_packages.price')
            ->having('orders_count', '>', 0) // Chỉ lấy dịch vụ có đơn hàng
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

    /**
     * Lấy thống kê tăng trưởng so với kỳ trước
     */
    public function getGrowthStats(Request $request): JsonResponse
    {
        Log::info('getGrowthStats called with params:', $request->all());

        $period = $request->get('period', 'month'); // day, week, month, year
        $date = $request->get('date', Carbon::today()->toDateString());

        try {
            $currentDate = Carbon::parse($date);
        } catch (\Exception $e) {
            Log::error('Invalid date format in getGrowthStats:', ['date' => $date, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Tính toán khoảng thời gian hiện tại và trước đó
        [$currentStart, $currentEnd, $previousStart, $previousEnd] = match ($period) {
            'day' => [
                $currentDate->copy()->startOfDay(),
                $currentDate->copy()->endOfDay(),
                $currentDate->copy()->subDay()->startOfDay(),
                $currentDate->copy()->subDay()->endOfDay(),
            ],
            'week' => [
                $currentDate->copy()->startOfWeek(Carbon::MONDAY),
                $currentDate->copy()->endOfWeek(Carbon::SUNDAY),
                $currentDate->copy()->subWeek()->startOfWeek(Carbon::MONDAY),
                $currentDate->copy()->subWeek()->endOfWeek(Carbon::SUNDAY),
            ],
            'year' => [
                $currentDate->copy()->startOfYear(),
                $currentDate->copy()->endOfYear(),
                $currentDate->copy()->subYear()->startOfYear(),
                $currentDate->copy()->subYear()->endOfYear(),
            ],
            default => [ // month
                $currentDate->copy()->startOfMonth(),
                $currentDate->copy()->endOfMonth(),
                $currentDate->copy()->subMonth()->startOfMonth(),
                $currentDate->copy()->subMonth()->endOfMonth(),
            ],
        };

        // Lấy dữ liệu kỳ hiện tại
        $currentStats = $this->getPeriodStats($currentStart, $currentEnd);
        // Lấy dữ liệu kỳ trước
        $previousStats = $this->getPeriodStats($previousStart, $previousEnd);

        // Tính toán tăng trưởng
        $growth = [
            'current_period' => [
                'start_date' => $currentStart->format('Y-m-d'),
                'end_date' => $currentEnd->format('Y-m-d'),
                'revenue' => $currentStats['revenue'],
                'orders' => $currentStats['orders'],
                'profit' => $currentStats['profit'],
                'customers' => $currentStats['customers'],
            ],
            'previous_period' => [
                'start_date' => $previousStart->format('Y-m-d'),
                'end_date' => $previousEnd->format('Y-m-d'),
                'revenue' => $previousStats['revenue'],
                'orders' => $previousStats['orders'],
                'profit' => $previousStats['profit'],
                'customers' => $previousStats['customers'],
            ],
            'growth' => [
                'revenue_growth' => $this->calculateGrowthPercent($currentStats['revenue'], $previousStats['revenue']),
                'orders_growth' => $this->calculateGrowthPercent($currentStats['orders'], $previousStats['orders']),
                'profit_growth' => $this->calculateGrowthPercent($currentStats['profit'], $previousStats['profit']),
                'customers_growth' => $this->calculateGrowthPercent($currentStats['customers'], $previousStats['customers']),
            ],
        ];

        return response()->json($growth);
    }

    /**
     * Lấy thống kê trong một khoảng thời gian
     */
    private function getPeriodStats($start, $end)
    {
        $revenue = CustomerService::join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->whereBetween('customer_services.created_at', [$start, $end])
            ->sum('service_packages.price');

        $orders = CustomerService::whereBetween('created_at', [$start, $end])->count();

        $profit = Profit::join('customer_services', 'profits.customer_service_id', '=', 'customer_services.id')
            ->whereBetween('customer_services.created_at', [$start, $end])
            ->sum('profits.profit_amount');

        $customers = CustomerService::whereBetween('created_at', [$start, $end])
            ->distinct('customer_id')
            ->count('customer_id');

        return [
            'revenue' => (float) $revenue,
            'orders' => (int) $orders,
            'profit' => (float) $profit,
            'customers' => (int) $customers,
        ];
    }

    /**
     * Tính phần trăm tăng trưởng
     */
    private function calculateGrowthPercent($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Lấy thống kê dự báo dựa trên trend
     */
    public function getForecastStats(Request $request): JsonResponse
    {
        $days = $request->get('days', 7); // Số ngày dự báo
        $baseDays = $request->get('base_days', 30); // Số ngày làm cơ sở tính toán

        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($baseDays);

        // Lấy dữ liệu lịch sử
        $historicalData = DB::table('customer_services')
            ->join('service_packages', 'customer_services.service_package_id', '=', 'service_packages.id')
            ->whereBetween('customer_services.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(customer_services.created_at) as date'),
                DB::raw('COUNT(customer_services.id) as orders'),
                DB::raw('SUM(service_packages.price) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($historicalData->count() < 7) {
            return response()->json(['error' => 'Không đủ dữ liệu để dự báo'], 400);
        }

        // Tính toán trend đơn giản (moving average)
        $avgDailyRevenue = $historicalData->avg('revenue');
        $avgDailyOrders = $historicalData->avg('orders');

        // Tính growth rate từ 7 ngày đầu và 7 ngày cuối
        $firstWeek = $historicalData->take(7);
        $lastWeek = $historicalData->reverse()->take(7);

        $firstWeekAvg = $firstWeek->avg('revenue');
        $lastWeekAvg = $lastWeek->avg('revenue');

        $growthRate = $firstWeekAvg > 0 ? (($lastWeekAvg - $firstWeekAvg) / $firstWeekAvg) : 0;

        // Dự báo cho các ngày tiếp theo
        $forecast = [];
        for ($i = 1; $i <= $days; $i++) {
            $forecastDate = $endDate->copy()->addDays($i);
            $projectedRevenue = $avgDailyRevenue * (1 + ($growthRate * ($i / 7))); // Áp dụng growth rate
            $projectedOrders = $avgDailyOrders * (1 + ($growthRate * ($i / 7)));

            $forecast[] = [
                'date' => $forecastDate->format('Y-m-d'),
                'projected_revenue' => round($projectedRevenue, 0),
                'projected_orders' => round($projectedOrders, 0),
                'confidence' => max(0, 100 - ($i * 5)), // Độ tin cậy giảm theo thời gian
            ];
        }

        return response()->json([
            'historical_data' => $historicalData,
            'forecast' => $forecast,
            'metrics' => [
                'avg_daily_revenue' => round($avgDailyRevenue, 0),
                'avg_daily_orders' => round($avgDailyOrders, 1),
                'growth_rate' => round($growthRate * 100, 2),
                'total_forecast_revenue' => round(collect($forecast)->sum('projected_revenue'), 0),
                'total_forecast_orders' => round(collect($forecast)->sum('projected_orders'), 0),
            ],
        ]);
    }
}
