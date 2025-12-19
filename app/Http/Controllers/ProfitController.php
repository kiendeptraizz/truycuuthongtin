<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\CustomerService;
use App\Models\Profit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfitController extends Controller
{
    /**
     * Hiển thị trang quản lý lợi nhuận
     */
    public function index(): View
    {
        return view('profits.index');
    }

    /**
     * Lấy danh sách đơn hàng trong ngày hiện tại
     */
    public function getTodayOrders(): JsonResponse
    {
        $today = Carbon::today();

        $orders = CustomerService::with(['customer', 'servicePackage', 'profit'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                // Thêm kiểm tra để tránh lỗi nếu thiếu thông tin customer hoặc servicePackage
                $customerName = $order->customer ? $order->customer->name : '[Khách hàng không tồn tại]';
                $customerCode = $order->customer ? $order->customer->customer_code : '';
                $serviceName = $order->servicePackage ? $order->servicePackage->name : '[Gói dịch vụ không tồn tại]';
                $price = $order->servicePackage ? $order->servicePackage->price : 0;

                return [
                    'id' => $order->id,
                    'customer_name' => $customerName,
                    'customer_code' => $customerCode,
                    'customer_display' => $customerCode ? "{$customerName} ({$customerCode})" : $customerName,
                    'service_name' => $serviceName,
                    'price' => $price,
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                    'profit_amount' => $order->profit ? $order->profit->profit_amount : null,
                    'has_profit' => $order->profit !== null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Lưu thông tin lợi nhuận cho đơn hàng
     */
    public function storeProfit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_service_id' => 'required|exists:customer_services,id',
            'profit_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ], [
            'customer_service_id.required' => 'ID đơn hàng là bắt buộc',
            'customer_service_id.exists' => 'Đơn hàng không tồn tại',
            'profit_amount.required' => 'Số tiền lãi là bắt buộc',
            'profit_amount.numeric' => 'Số tiền lãi phải là số',
            'profit_amount.min' => 'Số tiền lãi phải >= 0',
            'notes.max' => 'Ghi chú không được vượt quá 1000 ký tự'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Parse profit_amount để xóa dấu chấm phân cách hàng nghìn
            $profitAmount = parseCurrency($request->profit_amount);

            // Kiểm tra xem đã có profit cho đơn hàng này chưa
            $existingProfit = Profit::where('customer_service_id', $request->customer_service_id)->first();

            if ($existingProfit) {
                // Cập nhật profit hiện có
                $existingProfit->update([
                    'profit_amount' => $profitAmount,
                    'notes' => $request->notes,
                ]);
                $profit = $existingProfit;
            } else {
                // Tạo profit mới
                $profit = Profit::create([
                    'customer_service_id' => $request->customer_service_id,
                    'profit_amount' => $profitAmount,
                    'notes' => $request->notes,
                    'created_by' => auth()->guard('admin')->id() ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lưu thông tin lợi nhuận thành công',
                'data' => [
                    'profit_amount' => $profit->profit_amount,
                    'notes' => $profit->notes
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê lợi nhuận trong ngày
     */
    public function getTodayStatistics(): JsonResponse
    {
        $today = Carbon::today();

        // Lấy tất cả đơn hàng trong ngày
        $todayOrders = CustomerService::whereDate('created_at', $today)->count();

        // Lấy tổng lợi nhuận trong ngày
        $totalProfit = Profit::whereHas('customerService', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('profit_amount');

        // Đếm số đơn hàng đã nhập lãi
        $ordersWithProfit = Profit::whereHas('customerService', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->count();

        // Tính lợi nhuận trung bình
        $averageProfit = $ordersWithProfit > 0 ? $totalProfit / $ordersWithProfit : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_orders' => $todayOrders,
                'orders_with_profit' => $ordersWithProfit,
                'total_profit' => number_format($totalProfit, 0, ',', '.'),
                'average_profit' => number_format($averageProfit, 0, ',', '.'),
                'total_profit_raw' => $totalProfit,
                'average_profit_raw' => $averageProfit,
            ]
        ]);
    }

    /**
     * Xóa thông tin lợi nhuận
     */
    public function deleteProfit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_service_id' => 'required|exists:customer_services,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $profit = Profit::where('customer_service_id', $request->customer_service_id)->first();

            if (!$profit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin lợi nhuận'
                ], 404);
            }

            $profit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa thông tin lợi nhuận thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }
}
