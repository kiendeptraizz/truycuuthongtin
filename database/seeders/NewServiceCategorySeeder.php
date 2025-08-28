<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceCategory;

class NewServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Xóa tất cả danh mục cũ
            ServiceCategory::truncate();

            // Tạo các danh mục dịch vụ mới
            $categories = [
                [
                    'name' => 'AI',
                    'description' => 'Các dịch vụ AI như Chat GPT Plus, Supper Grok, PerPlexity Pro, Gemini Pro + 2TB Drive, Claude AI'
                ],
                [
                    'name' => 'AI làm video',
                    'description' => 'Các dịch vụ AI tạo video như Veo 3, Kling chính chủ, Hailuo, Gamma AI, Heygen AI, VidIQ boost'
                ],
                [
                    'name' => 'AI giọng đọc',
                    'description' => 'Các dịch vụ AI chuyển đổi văn bản thành giọng nói như Elevenlab, MiniMax'
                ],
                [
                    'name' => 'AI coding',
                    'description' => 'Các dịch vụ AI hỗ trợ lập trình như Cursor, Augment, Github Copilot'
                ],
                [
                    'name' => 'Công cụ làm việc',
                    'description' => 'Các công cụ hỗ trợ công việc như CapCut, CanVa Pro'
                ],
                [
                    'name' => 'Công cụ học tập',
                    'description' => 'Các công cụ hỗ trợ học tập như Doulingo, Quizlet, Drive 2TB + Gemini Pro, Coursera'
                ],
                [
                    'name' => 'Công cụ giải trí và xem phim',
                    'description' => 'Các dịch vụ giải trí như YouTuBe, Netflix, Vieon'
                ]
            ];

            foreach ($categories as $category) {
                ServiceCategory::create($category);
            }
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
