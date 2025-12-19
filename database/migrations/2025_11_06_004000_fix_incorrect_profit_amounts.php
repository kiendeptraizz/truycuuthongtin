<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Profit;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Lấy tất cả profit có giá trị < 10,000 (dữ liệu bị lưu sai - thiếu 3 số 0)
        $incorrectProfits = Profit::where('profit_amount', '<', 10000)->get();

        echo "\n=== BẮT ĐẦU SỬA LỖI PROFIT ===" . PHP_EOL;
        echo "Tìm thấy {$incorrectProfits->count()} profit cần sửa" . PHP_EOL . PHP_EOL;

        $fixedCount = 0;
        $totalBefore = 0;
        $totalAfter = 0;

        foreach ($incorrectProfits as $profit) {
            $oldAmount = $profit->profit_amount;
            $newAmount = $oldAmount * 1000;

            echo "Profit ID {$profit->id}: {$oldAmount} VND → {$newAmount} VND" . PHP_EOL;

            // Cập nhật giá trị
            $profit->update([
                'profit_amount' => $newAmount,
                'notes' => ($profit->notes ? $profit->notes . ' | ' : '') . 
                          'Đã sửa lỗi thiếu 3 số 0 (từ ' . number_format($oldAmount, 0, ',', '.') . ' → ' . 
                          number_format($newAmount, 0, ',', '.') . ') - ' . now()->format('d/m/Y H:i:s')
            ]);

            $totalBefore += $oldAmount;
            $totalAfter += $newAmount;
            $fixedCount++;
        }

        echo PHP_EOL;
        echo "=== KẾT QUẢ ===" . PHP_EOL;
        echo "Đã sửa: {$fixedCount} profit" . PHP_EOL;
        echo "Tổng trước khi sửa: " . number_format($totalBefore, 0, ',', '.') . " VND" . PHP_EOL;
        echo "Tổng sau khi sửa: " . number_format($totalAfter, 0, ',', '.') . " VND" . PHP_EOL;
        echo "Chênh lệch: " . number_format($totalAfter - $totalBefore, 0, ',', '.') . " VND" . PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không thể rollback vì không biết giá trị gốc nào là đúng
        echo "Không thể rollback migration này. Vui lòng sửa thủ công nếu cần." . PHP_EOL;
    }
};

