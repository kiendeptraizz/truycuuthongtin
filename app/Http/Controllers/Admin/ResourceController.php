<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCategory;
use App\Models\ResourceSubcategory;
use App\Models\ResourceAccount;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ResourceController extends Controller
{
    // =========================================================================
    // RESOURCE CATEGORIES
    // =========================================================================

    /**
     * Hiển thị danh sách danh mục tài nguyên
     */
    public function index(Request $request): View
    {
        $query = ResourceCategory::withCount(['accounts', 'accounts as available_accounts_count' => function ($q) {
            $q->where('is_available', true);
        }]);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->ordered()->paginate(15);

        // Thống kê tổng quan
        $stats = [
            'total_categories' => ResourceCategory::count(),
            'total_accounts' => ResourceAccount::count(),
            'available_accounts' => ResourceAccount::where('is_available', true)->count(),
            'expiring_soon' => ResourceAccount::available()->expiringSoon(7)->count(),
        ];

        return view('admin.resources.index', compact('categories', 'stats'));
    }

    /**
     * Form tạo danh mục mới
     */
    public function create(): View
    {
        return view('admin.resources.create');
    }

    /**
     * Lưu danh mục mới
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:resource_categories,name',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        ResourceCategory::create([
            'name' => $request->name,
            'icon' => $request->icon,
            'color' => $request->color ?? 'primary',
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.resources.index')
            ->with('success', 'Danh mục tài nguyên đã được tạo thành công!');
    }

    /**
     * Hiển thị chi tiết danh mục và danh sách tài khoản
     */
    public function show(Request $request, ResourceCategory $resource): View
    {
        $query = $resource->accounts()->with('subcategory');

        // Lọc theo danh mục con
        if ($request->filled('subcategory')) {
            if ($request->subcategory === 'none') {
                $query->whereNull('resource_subcategory_id');
            } else {
                $query->where('resource_subcategory_id', $request->subcategory);
            }
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo khả dụng
        if ($request->filled('available')) {
            $query->where('is_available', $request->available === '1');
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $accounts = $query->latest()->paginate(20);

        // Load subcategories
        $subcategories = $resource->subcategories;

        // Thống kê danh mục
        $categoryStats = [
            'total' => $resource->accounts()->count(),
            'available' => $resource->accounts()->where('is_available', true)->count(),
            'active' => $resource->accounts()->where('status', 'active')->count(),
            'expired' => $resource->accounts()->where('status', 'expired')->count(),
            'sold' => $resource->accounts()->where('status', 'sold')->count(),
            'expiring_soon' => $resource->accounts()->available()->expiringSoon(7)->count(),
        ];

        return view('admin.resources.show', compact('resource', 'accounts', 'categoryStats', 'subcategories'));
    }

    /**
     * Form chỉnh sửa danh mục
     */
    public function edit(ResourceCategory $resource): View
    {
        return view('admin.resources.edit', compact('resource'));
    }

    /**
     * Cập nhật danh mục
     */
    public function update(Request $request, ResourceCategory $resource): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:resource_categories,name,' . $resource->id,
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $resource->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'color' => $request->color,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.resources.index')
            ->with('success', 'Danh mục tài nguyên đã được cập nhật!');
    }

    /**
     * Xóa danh mục
     */
    public function destroy(ResourceCategory $resource): RedirectResponse
    {
        if ($resource->accounts()->count() > 0) {
            return redirect()->route('admin.resources.index')
                ->with('error', 'Không thể xóa danh mục đang có tài khoản!');
        }

        $resource->delete();

        return redirect()->route('admin.resources.index')
            ->with('success', 'Danh mục tài nguyên đã được xóa!');
    }

    // =========================================================================
    // RESOURCE ACCOUNTS
    // =========================================================================

    /**
     * Form tạo tài khoản mới trong danh mục
     */
    public function createAccount(ResourceCategory $resource): View
    {
        $subcategories = $resource->subcategories;
        return view('admin.resources.accounts.create', compact('resource', 'subcategories'));
    }

    /**
     * Lưu tài khoản mới
     */
    public function storeAccount(Request $request, ResourceCategory $resource): RedirectResponse
    {
        $request->validate([
            'resource_subcategory_id' => 'nullable|exists:resource_subcategories,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'two_factor_secret' => 'nullable|string',
            'recovery_codes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_available' => 'boolean',
            'status' => 'required|in:active,expired,sold,reserved,suspended',
            'notes' => 'nullable|string|max:2000',
        ]);

        $resource->accounts()->create([
            'resource_subcategory_id' => $request->resource_subcategory_id,
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password,
            'two_factor_secret' => $request->two_factor_secret,
            'recovery_codes' => $request->recovery_codes,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_available' => $request->boolean('is_available', true),
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', 'Tài khoản đã được thêm thành công!');
    }

    /**
     * Form chỉnh sửa tài khoản
     */
    public function editAccount(ResourceCategory $resource, ResourceAccount $account): View
    {
        $subcategories = $resource->subcategories;
        return view('admin.resources.accounts.edit', compact('resource', 'account', 'subcategories'));
    }

    /**
     * Cập nhật tài khoản
     */
    public function updateAccount(Request $request, ResourceCategory $resource, ResourceAccount $account): RedirectResponse
    {
        $request->validate([
            'resource_subcategory_id' => 'nullable|exists:resource_subcategories,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'two_factor_secret' => 'nullable|string',
            'recovery_codes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_available' => 'boolean',
            'status' => 'required|in:active,expired,sold,reserved,suspended',
            'notes' => 'nullable|string|max:2000',
        ]);

        $account->update([
            'resource_subcategory_id' => $request->resource_subcategory_id,
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password,
            'two_factor_secret' => $request->two_factor_secret,
            'recovery_codes' => $request->recovery_codes,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_available' => $request->boolean('is_available', true),
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', 'Tài khoản đã được cập nhật!');
    }

    /**
     * Xóa tài khoản
     */
    public function destroyAccount(ResourceCategory $resource, ResourceAccount $account): RedirectResponse
    {
        $account->delete();

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', 'Tài khoản đã được xóa!');
    }

    /**
     * Xóa hàng loạt tài khoản
     */
    public function bulkDeleteAccounts(Request $request, ResourceCategory $resource): RedirectResponse
    {
        $request->validate([
            'account_ids' => 'required|array|min:1',
            'account_ids.*' => 'exists:resource_accounts,id',
        ]);

        $count = ResourceAccount::where('resource_category_id', $resource->id)
            ->whereIn('id', $request->account_ids)
            ->delete();

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', "Đã xóa {$count} tài khoản thành công!");
    }

    /**
     * Cập nhật hàng loạt trạng thái tài khoản
     */
    public function bulkUpdateAccounts(Request $request, ResourceCategory $resource): RedirectResponse
    {
        $request->validate([
            'account_ids' => 'required|array|min:1',
            'account_ids.*' => 'exists:resource_accounts,id',
            'action' => 'required|in:mark_sold,mark_available,mark_unavailable,change_subcategory',
            'resource_subcategory_id' => 'nullable|exists:resource_subcategories,id',
        ]);

        $query = ResourceAccount::where('resource_category_id', $resource->id)
            ->whereIn('id', $request->account_ids);

        $count = 0;
        $actionText = '';

        switch ($request->action) {
            case 'mark_sold':
                $count = $query->update(['status' => 'sold', 'is_available' => false]);
                $actionText = 'đánh dấu đã bán';
                break;
            case 'mark_available':
                $count = $query->update(['is_available' => true]);
                $actionText = 'đánh dấu khả dụng';
                break;
            case 'mark_unavailable':
                $count = $query->update(['is_available' => false]);
                $actionText = 'đánh dấu không khả dụng';
                break;
            case 'change_subcategory':
                $count = $query->update(['resource_subcategory_id' => $request->resource_subcategory_id]);
                $actionText = 'chuyển danh mục con';
                break;
        }

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', "Đã {$actionText} {$count} tài khoản!");
    }

    /**
     * Toggle trạng thái khả dụng của tài khoản
     */
    public function toggleAvailable(ResourceCategory $resource, ResourceAccount $account): RedirectResponse
    {
        $account->update(['is_available' => !$account->is_available]);

        $status = $account->is_available ? 'khả dụng' : 'không khả dụng';

        return redirect()->back()
            ->with('success', "Đã đánh dấu tài khoản là {$status}!");
    }

    /**
     * Đánh dấu đã bán
     */
    public function markAsSold(ResourceCategory $resource, ResourceAccount $account): RedirectResponse
    {
        $account->update([
            'status' => 'sold',
            'is_available' => false,
        ]);

        return redirect()->back()
            ->with('success', 'Đã đánh dấu tài khoản là đã bán!');
    }

    /**
     * Cập nhật hàng loạt trạng thái hết hạn
     */
    public function updateExpiredAccounts(): RedirectResponse
    {
        $count = ResourceAccount::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->update([
                'status' => 'expired',
                'is_available' => false,
            ]);

        return redirect()->back()
            ->with('success', "Đã cập nhật {$count} tài khoản hết hạn!");
    }

    // =========================================================================
    // BULK IMPORT
    // =========================================================================

    /**
     * Form nhập hàng loạt tài khoản
     */
    public function bulkImportForm(ResourceCategory $resource): View
    {
        $subcategories = $resource->subcategories;
        return view('admin.resources.accounts.bulk-import', compact('resource', 'subcategories'));
    }

    /**
     * Xử lý nhập hàng loạt
     */
    public function bulkImport(Request $request, ResourceCategory $resource): RedirectResponse
    {
        $request->validate([
            'accounts_data' => 'required|string',
            'resource_subcategory_id' => 'nullable|exists:resource_subcategories,id',
            'format' => 'required|in:auto,email_pass,email_pass_2fa,custom',
            'delimiter' => 'nullable|string|max:5',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,expired,sold,reserved,suspended',
            'is_available' => 'boolean',
        ]);

        $data = trim($request->accounts_data);
        $lines = array_filter(explode("\n", $data), fn($line) => trim($line) !== '');

        $format = $request->format;
        $customDelimiter = $request->delimiter ?: '|';

        $imported = 0;
        $errors = [];
        $lineNumber = 0;

        foreach ($lines as $line) {
            $lineNumber++;
            $line = trim($line);

            if (empty($line)) continue;

            try {
                $parsed = $this->parseLine($line, $format, $customDelimiter);

                if (!$parsed) {
                    $errors[] = "Dòng {$lineNumber}: Không thể phân tích dữ liệu";
                    continue;
                }

                $resource->accounts()->create([
                    'resource_subcategory_id' => $request->resource_subcategory_id,
                    'email' => $parsed['email'] ?? null,
                    'password' => $parsed['password'] ?? null,
                    'two_factor_secret' => $parsed['2fa'] ?? null,
                    'recovery_codes' => $parsed['recovery'] ?? null,
                    'username' => $parsed['username'] ?? null,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'status' => $request->status,
                    'is_available' => $request->boolean('is_available', true),
                    'notes' => $parsed['notes'] ?? null,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Dòng {$lineNumber}: " . $e->getMessage();
            }
        }

        $message = "Đã nhập thành công {$imported} tài khoản!";

        if (count($errors) > 0) {
            $errorCount = count($errors);
            $message .= " ({$errorCount} dòng lỗi)";

            // Lưu errors vào session để hiển thị
            session()->flash('import_errors', array_slice($errors, 0, 10));
        }

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', $message);
    }

    /**
     * Phân tích một dòng dữ liệu
     */
    private function parseLine(string $line, string $format, string $delimiter): ?array
    {
        // Auto detect delimiter
        if ($format === 'auto') {
            // Thử các delimiter phổ biến
            $delimiters = ['|', ':', "\t", ';', ','];
            foreach ($delimiters as $del) {
                if (strpos($line, $del) !== false) {
                    $delimiter = $del;
                    break;
                }
            }
        }

        $parts = explode($delimiter, $line);
        $parts = array_map('trim', $parts);

        if (count($parts) < 1) {
            return null;
        }

        $result = [];

        switch ($format) {
            case 'email_pass':
                // email|pass
                $result['email'] = $parts[0] ?? null;
                $result['password'] = $parts[1] ?? null;
                break;

            case 'email_pass_2fa':
                // email|pass|2fa
                $result['email'] = $parts[0] ?? null;
                $result['password'] = $parts[1] ?? null;
                $result['2fa'] = $parts[2] ?? null;
                break;

            case 'custom':
            case 'auto':
            default:
                // Tự động detect dựa vào số lượng phần tử
                $count = count($parts);

                if ($count >= 1) $result['email'] = $parts[0];
                if ($count >= 2) $result['password'] = $parts[1];
                if ($count >= 3) $result['2fa'] = $parts[2];
                if ($count >= 4) $result['recovery'] = $parts[3];
                if ($count >= 5) $result['notes'] = implode(' ', array_slice($parts, 4));
                break;
        }

        return $result;
    }

    // =========================================================================
    // SUBCATEGORIES MANAGEMENT
    // =========================================================================

    /**
     * Lưu danh mục con mới (AJAX)
     */
    public function storeSubcategory(Request $request, ResourceCategory $resource): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        // Kiểm tra trùng tên trong category
        $exists = $resource->subcategories()->where('name', $request->name)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Danh mục con này đã tồn tại!'
            ], 422);
        }

        $subcategory = $resource->subcategories()->create([
            'name' => $request->name,
            'color' => $request->color ?? 'secondary',
            'description' => $request->description,
            'sort_order' => $resource->subcategories()->max('sort_order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo danh mục con thành công!',
            'subcategory' => $subcategory
        ]);
    }

    /**
     * Cập nhật danh mục con (AJAX)
     */
    public function updateSubcategory(Request $request, ResourceCategory $resource, ResourceSubcategory $subcategory): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        // Kiểm tra trùng tên trong category (trừ chính nó)
        $exists = $resource->subcategories()
            ->where('name', $request->name)
            ->where('id', '!=', $subcategory->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Danh mục con này đã tồn tại!'
            ], 422);
        }

        $subcategory->update([
            'name' => $request->name,
            'color' => $request->color ?? 'secondary',
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật danh mục con!',
            'subcategory' => $subcategory
        ]);
    }

    /**
     * Xóa danh mục con (AJAX)
     */
    public function destroySubcategory(ResourceCategory $resource, ResourceSubcategory $subcategory): \Illuminate\Http\JsonResponse
    {
        // Chuyển các accounts về không có subcategory
        ResourceAccount::where('resource_subcategory_id', $subcategory->id)
            ->update(['resource_subcategory_id' => null]);

        $subcategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa danh mục con!'
        ]);
    }

    /**
     * Lấy danh sách subcategories (AJAX)
     */
    public function getSubcategories(ResourceCategory $resource): \Illuminate\Http\JsonResponse
    {
        $subcategories = $resource->subcategories()->withCount('accounts')->get();

        return response()->json([
            'success' => true,
            'subcategories' => $subcategories
        ]);
    }
}
