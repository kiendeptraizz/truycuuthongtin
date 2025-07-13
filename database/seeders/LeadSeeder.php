<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\ServicePackage;
use App\Models\User;
use Faker\Factory as Faker;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN'); // Sử dụng locale Việt Nam

        // Lấy service packages và users để assign
        $servicePackages = ServicePackage::all();
        $users = User::all();

        // Danh sách tên Việt Nam
        $firstNames = [
            'Nguyễn',
            'Trần',
            'Lê',
            'Phạm',
            'Hoàng',
            'Huỳnh',
            'Phan',
            'Vũ',
            'Võ',
            'Đặng',
            'Bùi',
            'Đỗ',
            'Hồ',
            'Ngô',
            'Dương',
            'Lý',
            'Mai',
            'Lâm',
            'Tạ',
            'Đinh'
        ];

        $lastNames = [
            'Văn An',
            'Thị Bình',
            'Minh Cường',
            'Thị Dung',
            'Văn Đức',
            'Thị Hoa',
            'Minh Khoa',
            'Thị Lan',
            'Văn Minh',
            'Thị Nga',
            'Minh Phúc',
            'Thị Quỳnh',
            'Văn Sơn',
            'Thị Tâm',
            'Minh Tuấn',
            'Thị Uyên',
            'Văn Việt',
            'Thị Xuân',
            'Minh Yến',
            'Văn Đạt',
            'Thị Hằng',
            'Minh Hải',
            'Thị Linh',
            'Văn Nam',
            'Thị Oanh',
            'Minh Phong',
            'Thị Quyên',
            'Văn Rồng',
            'Thị Sương',
            'Minh Tài'
        ];

        // Danh sách nguồn lead
        $sources = [
            'facebook',
            'zalo',
            'website',
            'google_ads',
            'referral',
            'phone',
            'walk_in',
            'tiktok',
            'instagram',
            'email_marketing'
        ];

        // Danh sách trạng thái với tỷ lệ thực tế
        $statuses = [
            'new' => 20,      // 20 lead mới
            'contacted' => 15, // 15 đã liên hệ
            'interested' => 8, // 8 quan tâm
            'quoted' => 4,     // 4 đã báo giá
            'negotiating' => 2, // 2 đang đàm phán
            'won' => 1,        // 1 thành công
            'lost' => 3,       // 3 thất bại
            'follow_up' => 5   // 5 cần theo dõi
        ];

        // Danh sách độ ưu tiên
        $priorities = ['low', 'medium', 'high', 'urgent'];

        // Danh sách yêu cầu khách hàng
        $requirements = [
            'Cần tư vấn gói dịch vụ phù hợp',
            'Muốn biết chi phí chi tiết',
            'Quan tâm đến chất lượng dịch vụ',
            'Cần hỗ trợ kỹ thuật',
            'Muốn xem demo sản phẩm',
            'Cần báo giá cho nhiều gói',
            'Quan tâm đến thời gian triển khai',
            'Muốn được tư vấn trực tiếp',
            'Cần so sánh với đối thủ',
            'Quan tâm đến chính sách hậu mãi'
        ];

        $leadCount = 0;

        // Tạo lead theo tỷ lệ trạng thái
        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $firstName = $faker->randomElement($firstNames);
                $lastName = $faker->randomElement($lastNames);
                $fullName = $firstName . ' ' . $lastName;

                // Tạo số điện thoại Việt Nam
                $phoneFormats = ['098', '097', '096', '086', '032', '033', '034', '035', '036', '037', '038', '039'];
                $phone = $faker->randomElement($phoneFormats) . $faker->numerify('#######');

                // Tạo email
                $email = null;
                if ($faker->boolean(70)) { // 70% có email
                    $emailName = $this->removeVietnameseAccents(strtolower(str_replace(' ', '', $lastName)));
                    $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
                    $email = $emailName . $faker->numberBetween(1990, 2005) . '@' . $faker->randomElement($domains);
                }

                // Tạo giá trị ước tính
                $estimatedValue = null;
                $servicePackageId = null;
                if ($servicePackages->count() > 0 && $faker->boolean(80)) {
                    $servicePackage = $faker->randomElement($servicePackages);
                    $servicePackageId = $servicePackage->id;
                    $estimatedValue = $servicePackage->price * $faker->randomFloat(2, 0.8, 1.2); // ±20% giá gốc
                }

                // Tạo thời gian theo dõi
                $nextFollowUpAt = null;
                $lastContactAt = null;
                $convertedAt = null;

                if (in_array($status, ['contacted', 'interested', 'quoted', 'negotiating', 'follow_up'])) {
                    $lastContactAt = $faker->dateTimeBetween('-30 days', 'now');

                    if ($status === 'follow_up' || $faker->boolean(60)) {
                        $nextFollowUpAt = $faker->dateTimeBetween('now', '+7 days');
                    }
                }

                if ($status === 'won') {
                    $convertedAt = $faker->dateTimeBetween('-30 days', 'now');
                    $lastContactAt = $faker->dateTimeBetween('-30 days', $convertedAt);
                }

                // Tạo lead
                $lead = Lead::create([
                    'name' => $fullName,
                    'phone' => $phone,
                    'email' => $email,
                    'source' => $faker->randomElement($sources),
                    'status' => $status,
                    'priority' => $faker->randomElement($priorities),
                    'notes' => $faker->boolean(70) ? $faker->paragraph(2) : null,
                    'requirements' => $faker->boolean(80) ? $faker->randomElement($requirements) : null,
                    'estimated_value' => $estimatedValue,
                    'service_package_id' => $servicePackageId,
                    'assigned_to' => $users->count() > 0 ? $faker->randomElement($users)->id : null,
                    'last_contact_at' => $lastContactAt,
                    'next_follow_up_at' => $nextFollowUpAt,
                    'converted_at' => $convertedAt,
                    'created_at' => $faker->dateTimeBetween('-60 days', 'now'),
                ]);

                // Tạo một số hoạt động cho lead
                $this->createLeadActivities($lead, $faker);

                $leadCount++;
            }
        }

        // Tạo thêm một số lead quá hạn để test
        for ($i = 0; $i < 3; $i++) {
            $firstName = $faker->randomElement($firstNames);
            $lastName = $faker->randomElement($lastNames);
            $fullName = $firstName . ' ' . $lastName;

            $phoneFormats = ['098', '097', '096', '086', '032', '033', '034', '035', '036', '037', '038', '039'];
            $phone = $faker->randomElement($phoneFormats) . $faker->numerify('#######');

            Lead::create([
                'name' => $fullName,
                'phone' => $phone,
                'email' => $faker->boolean(70) ? $faker->email : null,
                'source' => $faker->randomElement($sources),
                'status' => $faker->randomElement(['contacted', 'interested', 'follow_up']),
                'priority' => $faker->randomElement(['high', 'urgent']),
                'notes' => 'Lead quan trọng cần theo dõi gấp',
                'requirements' => $faker->randomElement($requirements),
                'estimated_value' => $faker->numberBetween(5000000, 20000000),
                'service_package_id' => $servicePackages->count() > 0 ? $faker->randomElement($servicePackages)->id : null,
                'assigned_to' => $users->count() > 0 ? $faker->randomElement($users)->id : null,
                'last_contact_at' => $faker->dateTimeBetween('-15 days', '-7 days'),
                'next_follow_up_at' => $faker->dateTimeBetween('-5 days', '-1 days'), // Quá hạn
                'created_at' => $faker->dateTimeBetween('-30 days', '-15 days'),
            ]);

            $leadCount++;
        }

        $this->command->info("Đã tạo thành công {$leadCount} lead!");
    }

    /**
     * Tạo hoạt động cho lead
     */
    private function createLeadActivities($lead, $faker)
    {
        $users = User::all();
        $activityTypes = ['call', 'email', 'meeting', 'note', 'quote', 'follow_up'];

        // Tạo 1-5 hoạt động cho mỗi lead
        $activityCount = $faker->numberBetween(1, 5);

        for ($i = 0; $i < $activityCount; $i++) {
            $type = $faker->randomElement($activityTypes);
            $notes = $this->generateActivityNotes($type, $faker);

            $lead->activities()->create([
                'type' => $type,
                'notes' => $notes,
                'data' => $faker->boolean(30) ? ['duration' => $faker->numberBetween(5, 60)] : null,
                'user_id' => $users->count() > 0 ? $faker->randomElement($users)->id : 1,
                'created_at' => $faker->dateTimeBetween($lead->created_at, 'now'),
            ]);
        }
    }

    /**
     * Tạo ghi chú hoạt động theo loại
     */
    private function generateActivityNotes($type, $faker)
    {
        $notes = [
            'call' => [
                'Gọi điện tư vấn, khách hàng quan tâm',
                'Liên hệ qua điện thoại, hẹn gặp trực tiếp',
                'Gọi điện nhưng không liên lạc được',
                'Khách hàng yêu cầu gọi lại vào buổi chiều',
                'Tư vấn qua điện thoại về gói dịch vụ'
            ],
            'email' => [
                'Gửi email báo giá chi tiết',
                'Gửi thông tin sản phẩm qua email',
                'Khách hàng đã phản hồi email',
                'Gửi email cảm ơn và theo dõi',
                'Gửi email nhắc nhở hẹn gặp'
            ],
            'meeting' => [
                'Họp trực tiếp tại văn phòng',
                'Gặp khách hàng tại quán cafe',
                'Họp online qua Zoom',
                'Thuyết trình sản phẩm cho khách hàng',
                'Gặp gỡ và tư vấn chi tiết'
            ],
            'note' => [
                'Khách hàng cần thời gian suy nghĩ',
                'Yêu cầu so sánh với đối thủ cạnh tranh',
                'Quan tâm đến chính sách hậu mãi',
                'Cần tư vấn thêm về kỹ thuật',
                'Ghi chú về ngân sách của khách hàng'
            ],
            'quote' => [
                'Gửi báo giá chi tiết cho khách hàng',
                'Cập nhật báo giá theo yêu cầu mới',
                'Báo giá cho nhiều gói khác nhau',
                'Điều chỉnh giá theo ngân sách KH',
                'Báo giá bao gồm chi phí bảo trì'
            ],
            'follow_up' => [
                'Theo dõi phản hồi từ khách hàng',
                'Nhắc nhở về báo giá đã gửi',
                'Hỏi thăm tình hình quyết định',
                'Cung cấp thông tin bổ sung',
                'Lên lịch gặp lại khách hàng'
            ]
        ];

        return $faker->randomElement($notes[$type]);
    }

    /**
     * Loại bỏ dấu tiếng Việt
     */
    private function removeVietnameseAccents($str)
    {
        $accents = [
            'à',
            'á',
            'ạ',
            'ả',
            'ã',
            'â',
            'ầ',
            'ấ',
            'ậ',
            'ẩ',
            'ẫ',
            'ă',
            'ằ',
            'ắ',
            'ặ',
            'ẳ',
            'ẵ',
            'è',
            'é',
            'ẹ',
            'ẻ',
            'ẽ',
            'ê',
            'ề',
            'ế',
            'ệ',
            'ể',
            'ễ',
            'ì',
            'í',
            'ị',
            'ỉ',
            'ĩ',
            'ò',
            'ó',
            'ọ',
            'ỏ',
            'õ',
            'ô',
            'ồ',
            'ố',
            'ộ',
            'ổ',
            'ỗ',
            'ơ',
            'ờ',
            'ớ',
            'ợ',
            'ở',
            'ỡ',
            'ù',
            'ú',
            'ụ',
            'ủ',
            'ũ',
            'ư',
            'ừ',
            'ứ',
            'ự',
            'ử',
            'ữ',
            'ỳ',
            'ý',
            'ỵ',
            'ỷ',
            'ỹ',
            'đ'
        ];

        $noAccents = [
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'i',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'y',
            'y',
            'y',
            'y',
            'y',
            'd'
        ];

        return str_replace($accents, $noAccents, $str);
    }
}
