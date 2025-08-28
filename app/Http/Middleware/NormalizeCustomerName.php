<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeCustomerName
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Chỉ áp dụng cho các request tạo/cập nhật khách hàng
        if ($this->shouldNormalizeName($request)) {
            $this->normalizeNameInRequest($request);
        }

        return $next($request);
    }

    /**
     * Kiểm tra xem có nên chuẩn hóa tên không
     */
    private function shouldNormalizeName(Request $request): bool
    {
        $route = $request->route();
        
        if (!$route) {
            return false;
        }

        // Kiểm tra route liên quan đến customers
        $routeName = $route->getName();
        $isCustomerRoute = str_contains($routeName ?? '', 'customers');
        
        // Kiểm tra method POST/PUT/PATCH
        $isModifyingRequest = in_array($request->method(), ['POST', 'PUT', 'PATCH']);
        
        // Kiểm tra có field 'name' trong request
        $hasNameField = $request->has('name');

        return $isCustomerRoute && $isModifyingRequest && $hasNameField;
    }

    /**
     * Chuẩn hóa tên trong request
     */
    private function normalizeNameInRequest(Request $request): void
    {
        $name = $request->input('name');
        
        if ($name) {
            $normalizedName = $this->normalizeName($name);
            $request->merge(['name' => $normalizedName]);
        }
    }

    /**
     * Chuẩn hóa tên khách hàng
     */
    private function normalizeName(string $name): string
    {
        // 1. Trim whitespace
        $name = trim($name);
        
        // 2. Loại bỏ nhiều dấu cách liên tiếp
        $name = preg_replace('/\s+/', ' ', $name);
        
        // 3. Chuẩn hóa chữ cái đầu mỗi từ (Title Case)
        $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
        
        // 4. Xử lý các trường hợp đặc biệt cho tiếng Việt
        // Đảm bảo chữ "Đ" và "đ" được giữ nguyên
        $name = str_replace(['Đ', 'đ'], ['Đ', 'đ'], $name);
        
        // 5. Xử lý các từ viết tắt thường gặp
        $name = $this->handleAbbreviations($name);
        
        return $name;
    }

    /**
     * Xử lý các từ viết tắt thường gặp
     */
    private function handleAbbreviations(string $name): string
    {
        // Danh sách các từ viết tắt thường gặp
        $abbreviations = [
            'Tv' => 'TV',
            'Ai' => 'AI', 
            'It' => 'IT',
            'Ceo' => 'CEO',
            'Cto' => 'CTO',
            'Hr' => 'HR',
        ];

        foreach ($abbreviations as $wrong => $correct) {
            $name = str_replace(' ' . $wrong . ' ', ' ' . $correct . ' ', $name);
            $name = str_replace(' ' . $wrong, ' ' . $correct, $name);
            
            // Xử lý trường hợp ở đầu chuỗi
            if (str_starts_with($name, $wrong . ' ')) {
                $name = $correct . substr($name, strlen($wrong));
            }
            
            // Xử lý trường hợp ở cuối chuỗi
            if (str_ends_with($name, ' ' . $wrong)) {
                $name = substr($name, 0, -strlen($wrong)) . $correct;
            }
        }

        return $name;
    }
}
