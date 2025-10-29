<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ZaloAccount;
use App\Models\TargetGroup;
use App\Models\GroupMember;
use App\Models\MessageCampaign;
use App\Models\MessageLog;
use App\Models\ConversionLog;
use Carbon\Carbon;

class ZaloMarketingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo Zalo Accounts
        $account1 = ZaloAccount::create([
            'account_name' => 'Tài khoản Marketing 1',
            'email_or_phone' => '0901234567',
            'password' => 'password123',
            'daily_message_limit' => 100,
            'messages_sent_today' => 45,
            'status' => 'active',
            'last_message_date' => now(),
            'notes' => 'Tài khoản chính cho chiến dịch'
        ]);

        $account2 = ZaloAccount::create([
            'account_name' => 'Tài khoản Marketing 2',
            'email_or_phone' => '0907654321',
            'password' => 'password456',
            'daily_message_limit' => 80,
            'messages_sent_today' => 30,
            'status' => 'active',
            'last_message_date' => now(),
            'notes' => 'Tài khoản phụ'
        ]);

        // Tạo Target Groups (Nhóm đối thủ)
        $competitorGroup1 = TargetGroup::create([
            'group_name' => 'Nhóm học tiếng Anh ABC',
            'group_link' => 'https://zalo.me/g/abc123',
            'group_id' => 'abc123',
            'topic' => 'Tiếng Anh giao tiếp',
            'total_members' => 1500,
            'group_type' => 'competitor',
            'status' => 'active',
            'opening_date' => now()->subMonths(2),
            'last_scanned_at' => now()->subDays(1),
            'description' => 'Nhóm học tiếng Anh của đối thủ, có nhiều học viên tiềm năng'
        ]);

        $competitorGroup2 = TargetGroup::create([
            'group_name' => 'Cộng đồng học tiếng Anh XYZ',
            'group_link' => 'https://zalo.me/g/xyz456',
            'group_id' => 'xyz456',
            'topic' => 'IELTS - TOEIC',
            'total_members' => 2300,
            'group_type' => 'competitor',
            'status' => 'active',
            'opening_date' => now()->subMonths(3),
            'last_scanned_at' => now()->subDays(2),
            'description' => 'Nhóm tập trung vào IELTS và TOEIC'
        ]);

        // Tạo Own Groups (Nhóm của mình)
        $ownGroup1 = TargetGroup::create([
            'group_name' => 'Học tiếng Anh Online - Miễn phí',
            'group_link' => 'https://zalo.me/g/mygroup001',
            'group_id' => 'mygroup001',
            'topic' => 'Tiếng Anh giao tiếp online',
            'total_members' => 350,
            'group_type' => 'own',
            'status' => 'active',
            'opening_date' => now()->addDays(5),
            'description' => 'Nhóm học tiếng Anh của chúng tôi - miễn phí 100%'
        ]);

        $ownGroup2 = TargetGroup::create([
            'group_name' => 'IELTS 7.0+ cùng chuyên gia',
            'group_link' => 'https://zalo.me/g/mygroup002',
            'group_id' => 'mygroup002',
            'topic' => 'IELTS nâng cao',
            'total_members' => 120,
            'group_type' => 'own',
            'status' => 'active',
            'opening_date' => now()->addDays(10),
            'description' => 'Khóa IELTS chuyên sâu'
        ]);

        // Tạo Group Members
        $members = [];
        for ($i = 1; $i <= 50; $i++) {
            $member = GroupMember::create([
                'target_group_id' => $competitorGroup1->id,
                'zalo_id' => 'user_' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'display_name' => 'Thành viên ' . $i,
                'phone_number' => '090' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'status' => $i <= 30 ? 'contacted' : 'new',
                'joined_at' => now()->subDays(rand(1, 30)),
                'last_contacted_at' => $i <= 30 ? now()->subDays(rand(1, 5)) : null,
                'contact_count' => $i <= 30 ? rand(1, 3) : 0
            ]);
            $members[] = $member;
        }

        // Tạo Message Campaigns
        $campaign1 = MessageCampaign::create([
            'campaign_name' => 'Chiến dịch tháng 10 - Tiếng Anh giao tiếp',
            'target_group_id' => $competitorGroup1->id,
            'own_group_id' => $ownGroup1->id,
            'message_template' => "Chào {name}, mình thấy bạn trong nhóm {group_name}.\n\nMình có lớp học tiếng Anh giao tiếp MIỄN PHÍ khai giảng ngày 27/10. Bạn có muốn tham gia không?\n\nLink đăng ký: https://zalo.me/g/mygroup001",
            'start_date' => now()->subDays(7),
            'end_date' => now()->addDays(7),
            'daily_target' => 50,
            'status' => 'active',
            'total_sent' => 150,
            'total_delivered' => 145,
            'total_failed' => 5,
            'total_converted' => 12,
            'conversion_rate' => 8.28
        ]);

        $campaign2 = MessageCampaign::create([
            'campaign_name' => 'Chiến dịch IELTS Premium',
            'target_group_id' => $competitorGroup2->id,
            'own_group_id' => $ownGroup2->id,
            'message_template' => "Hi {name}!\n\nBạn đang chuẩn bị thi IELTS? Mình có khóa học với giáo viên 8.5 IELTS, khai giảng sớm.\n\nQuan tâm thì inbox mình nhé!",
            'start_date' => now()->subDays(3),
            'end_date' => now()->addDays(10),
            'daily_target' => 30,
            'status' => 'active',
            'total_sent' => 90,
            'total_delivered' => 87,
            'total_failed' => 3,
            'total_converted' => 5,
            'conversion_rate' => 5.75
        ]);

        // Tạo Message Logs
        foreach ($members as $index => $member) {
            if ($index < 30) { // 30 members đầu đã được gửi tin
                $sentAt = now()->subDays(rand(1, 7));
                
                MessageLog::create([
                    'campaign_id' => $campaign1->id,
                    'zalo_account_id' => $index % 2 == 0 ? $account1->id : $account2->id,
                    'group_member_id' => $member->id,
                    'message_content' => str_replace(
                        ['{name}', '{group_name}'],
                        [$member->display_name, $competitorGroup1->group_name],
                        $campaign1->message_template
                    ),
                    'status' => $index < 28 ? 'delivered' : 'failed',
                    'error_message' => $index >= 28 ? 'User blocked messages' : null,
                    'sent_at' => $sentAt,
                    'delivered_at' => $index < 28 ? $sentAt->addSeconds(rand(1, 10)) : null,
                ]);
            }
        }

        // Tạo Conversion Logs
        $convertedMembers = collect($members)->random(12);
        foreach ($convertedMembers as $index => $member) {
            $messageLog = MessageLog::where('group_member_id', $member->id)->first();
            
            if ($messageLog) {
                $joinedAt = $messageLog->sent_at->addDays(rand(1, 5));
                
                ConversionLog::create([
                    'campaign_id' => $campaign1->id,
                    'group_member_id' => $member->id,
                    'message_log_id' => $messageLog->id,
                    'own_group_id' => $ownGroup1->id,
                    'joined_at' => $joinedAt,
                    'notes' => 'Tham gia sau ' . $messageLog->sent_at->diffInDays($joinedAt) . ' ngày'
                ]);

                // Update member status
                $member->status = 'converted';
                $member->save();
            }
        }

        $this->command->info('✅ Đã tạo dữ liệu mẫu cho Zalo Marketing System!');
        $this->command->info('📊 Thống kê:');
        $this->command->info('- Tài khoản Zalo: ' . ZaloAccount::count());
        $this->command->info('- Nhóm mục tiêu: ' . TargetGroup::count());
        $this->command->info('- Thành viên: ' . GroupMember::count());
        $this->command->info('- Chiến dịch: ' . MessageCampaign::count());
        $this->command->info('- Tin nhắn: ' . MessageLog::count());
        $this->command->info('- Chuyển đổi: ' . ConversionLog::count());
    }
}

