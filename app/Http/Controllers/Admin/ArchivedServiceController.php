<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ArchivedServiceController extends Controller
{
    /**
     * Hiển thị danh sách dịch vụ đã xóa (lưu trữ)
     */
    public function index(Request $request)
    {
        $query = CustomerService::onlyTrashed()
            ->with(['customer', 'servicePackage.category']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('customer_code', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('servicePackage', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                ->orWhere('login_email', 'like', "%{$search}%");
            });
        }

        // Lọc theo thời gian xóa
        if ($request->filled('deleted_from')) {
            $query->whereDate('deleted_at', '>=', $request->deleted_from);
        }
        if ($request->filled('deleted_to')) {
            $query->whereDate('deleted_at', '<=', $request->deleted_to);
        }

        $archivedServices = $query->orderBy('deleted_at', 'desc')->paginate(20);

        // Thống kê
        $stats = [
            'total' => CustomerService::onlyTrashed()->count(),
            'last_7_days' => CustomerService::onlyTrashed()
                ->where('deleted_at', '>=', now()->subDays(7))
                ->count(),
            'last_30_days' => CustomerService::onlyTrashed()
                ->where('deleted_at', '>=', now()->subDays(30))
                ->count(),
        ];

        return view('admin.archived-services.index', compact('archivedServices', 'stats'));
    }

    /**
     * Khôi phục dịch vụ đã xóa
     */
    public function restore($id)
    {
        $service = CustomerService::onlyTrashed()->findOrFail($id);
        $service->restore();

        return redirect()->back()
            ->with('success', "Đã khôi phục dịch vụ cho khách hàng {$service->customer->name}");
    }

    /**
     * Xóa vĩnh viễn dịch vụ
     */
    public function forceDelete($id)
    {
        $service = CustomerService::onlyTrashed()->findOrFail($id);
        $customerName = $service->customer->name ?? 'N/A';
        $service->forceDelete();

        return redirect()->back()
            ->with('success', "Đã xóa vĩnh viễn dịch vụ của khách hàng {$customerName}");
    }

    /**
     * Khôi phục nhiều dịch vụ
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customer_services,id',
        ]);

        $restoredCount = CustomerService::onlyTrashed()
            ->whereIn('id', $request->ids)
            ->restore();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã khôi phục {$restoredCount} dịch vụ."
            ]);
        }

        return redirect()->back()
            ->with('success', "Đã khôi phục {$restoredCount} dịch vụ.");
    }

    /**
     * Xóa vĩnh viễn nhiều dịch vụ
     */
    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:customer_services,id',
        ]);

        $deletedCount = 0;
        $services = CustomerService::onlyTrashed()->whereIn('id', $request->ids)->get();
        
        foreach ($services as $service) {
            $service->forceDelete();
            $deletedCount++;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xóa vĩnh viễn {$deletedCount} dịch vụ."
            ]);
        }

        return redirect()->back()
            ->with('success', "Đã xóa vĩnh viễn {$deletedCount} dịch vụ.");
    }

    /**
     * Chạy cleanup command thủ công
     */
    public function runCleanup(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $dryRun = $request->boolean('dry_run', false);

        try {
            // Đếm số dịch vụ sẽ bị xóa
            $expiredCount = CustomerService::expiredMoreThanDays($days)->count();

            if ($expiredCount === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Không có dịch vụ nào hết hạn quá ' . $days . ' ngày cần xóa.',
                    'deleted_count' => 0
                ]);
            }

            if ($dryRun) {
                return response()->json([
                    'success' => true,
                    'message' => "Tìm thấy {$expiredCount} dịch vụ hết hạn quá {$days} ngày.",
                    'preview_count' => $expiredCount,
                    'dry_run' => true
                ]);
            }

            // Chạy command
            Artisan::call('services:cleanup-expired', [
                '--days' => $days,
                '--no-interaction' => true
            ]);

            Log::info("Manual cleanup executed", [
                'days' => $days,
                'deleted_count' => $expiredCount,
                'executed_by' => auth()->user()->email ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Đã xóa thành công {$expiredCount} dịch vụ hết hạn quá {$days} ngày.",
                'deleted_count' => $expiredCount
            ]);

        } catch (\Exception $e) {
            Log::error("Cleanup error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê dịch vụ hết hạn theo số ngày
     */
    public function getExpiredStats()
    {
        $stats = [
            '7_days' => CustomerService::expiredMoreThanDays(7)->count(),
            '14_days' => CustomerService::expiredMoreThanDays(14)->count(),
            '30_days' => CustomerService::expiredMoreThanDays(30)->count(),
            '60_days' => CustomerService::expiredMoreThanDays(60)->count(),
            '90_days' => CustomerService::expiredMoreThanDays(90)->count(),
        ];

        return response()->json($stats);
    }
}

