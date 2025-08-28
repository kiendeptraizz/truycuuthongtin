<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\FamilyAccount;
use App\Models\FamilyMember;
use App\Models\ServicePackage;
use App\Models\Customer;

class AddFamilyAccountsData extends Command
{
    protected $signature = 'family:add-data';
    protected $description = 'Thêm dữ liệu tài khoản gia đình mẫu';

    public function handle()
    {
        $this->info('=== Thêm dữ liệu tài khoản gia đình ===');

        // Lấy service package đầu tiên để sử dụng
        $servicePackage = ServicePackage::first();
        if (!$servicePackage) {
            $this->error('Không tìm thấy service package nào. Vui lòng tạo service package trước.');
            return 1;
        }

        $this->info("Sử dụng Service Package: {$servicePackage->name} (ID: {$servicePackage->id})");

        // Dữ liệu 7 nhóm gia đình
        $familyGroups = [
            [
                'owner' => 'hoangthanh13232@gmail.com',
                'members' => [
                    'misuclosetshop@gmail.com',
                    '10423154@student.vgu.edu.vn',
                    'roknghean2@gmail.com',
                    'xuanhaohiu@gmail.com',
                    'sacmaunhoccompany@gmail.com'
                ]
            ],
            [
                'owner' => 'kieusinh2297@gmail.com',
                'members' => [
                    'bacto3526@gmail.com',
                    'minhduc12589@gmail.com',
                    'topjobfpt@gmail.com',
                    'deliciouscookie20@gmail.com',
                    'thinhnguyenydhp@gmail.com'
                ]
            ],
            [
                'owner' => 'macoanh2082@gmail.com',
                'members' => [
                    'nguyenthiminhanh.ltcd24@sptwnt.edu.vn',
                    '64jxbc2c@taikhoanvip.io.vn',
                    'thieugiadeptrai1994@gmail.com',
                    'dattao11032003@gmail.com',
                    'sonhuynh23011991@gmail.com',
                    'levanvi.chatgpt@gmail.com',
                    'khoitrandang0312@gmail.com'
                ]
            ],
            [
                'owner' => 'vongthu250468@gmail.com',
                'members' => [
                    '64jxbc2c@taikhoanvip.io.vn',
                    'nguyenhai982003@gmail.com',
                    'nguyenlanoanhh@gmail.com',
                    'chulchul11028@gmail.com',
                    'vntraveladvisory@gmail.com',
                    'hainguyenthi2110@gmail.com',
                    'cuong.vtcdtvt58@gmail.com'
                ]
            ],
            [
                'owner' => 'phanthien6098375@gmail.com',
                'members' => [
                    'vmphuong1110@gmail.com',
                    'thephong.hust@gmail.com',
                    'dothikimtuyen03081984@gmail.com'
                ]
            ],
            [
                'owner' => 'phantom827@wotomail.com',
                'members' => [
                    'joearmitage@gobuybox.com',
                    'ducminhhuynh0305@gmail.com',
                    'congchuamituot2011@gmail.com',
                    'nxnt2016@gmail.com',
                    'lnhuuquinh1609@gmail.com',
                    'thgo0202@gmail.com'
                ]
            ],
            [
                'owner' => 'hendrikdekker434@gmail.com',
                'members' => [
                    'buixuanhien020244@gmail.com',
                    'joearmitage@gobuybox.com',
                    'huyhuy28052003@gmail.com',
                    'gaschburdab0@outlook.com',
                    'uyengiagoodluck1@gmail.com',
                    'ptattmpxd01012004@gmail.com'
                ]
            ]
        ];

        $createdFamilies = 0;
        $createdMembers = 0;

        DB::beginTransaction();

        try {
            foreach ($familyGroups as $index => $group) {
                $groupNumber = $index + 1;
                $this->info("Đang tạo nhóm gia đình {$groupNumber}...");
                
                // Tạo family account
                $familyAccount = FamilyAccount::create([
                    'family_name' => "Gia đình {$groupNumber}",
                    'service_package_id' => $servicePackage->id,
                    'owner_email' => $group['owner'],
                    'owner_name' => "Chủ gia đình {$groupNumber}",
                    'max_members' => count($group['members']) + 1, // +1 cho owner
                    'current_members' => 0, // Sẽ được cập nhật tự động
                    'activated_at' => now(),
                    'expires_at' => now()->addYear(), // Đặt hết hạn 1 năm sau
                    'status' => 'active',
                    'family_notes' => "Nhóm gia đình {$groupNumber} - Tự động tạo",
                    'internal_notes' => "Được tạo tự động từ command",
                    'created_by' => null,
                    'managed_by' => null
                ]);
                
                $createdFamilies++;
                $this->line("  ✓ Tạo family account: {$familyAccount->family_name} (Code: {$familyAccount->family_code})");
                
                // Tạo owner member
                $ownerCustomer = Customer::firstOrCreate(
                    ['email' => $group['owner']],
                    [
                        'name' => "Chủ gia đình {$groupNumber}",
                        'phone' => '',
                        'address' => '',
                        'notes' => 'Tự động tạo cho family account'
                    ]
                );
                
                $ownerMember = FamilyMember::create([
                    'family_account_id' => $familyAccount->id,
                    'customer_id' => $ownerCustomer->id,
                    'member_email' => $group['owner'],
                    'member_role' => 'owner',
                    'status' => 'active',
                    'start_date' => now()->toDateString(),
                    'end_date' => null, // Để trống như yêu cầu
                    'member_notes' => 'Chủ gia đình',
                    'internal_notes' => 'Được tạo tự động từ command',
                    'added_by' => null
                ]);
                
                $createdMembers++;
                $this->line("    ✓ Tạo owner: {$group['owner']}");
                
                // Tạo các thành viên
                foreach ($group['members'] as $memberEmail) {
                    $memberCustomer = Customer::firstOrCreate(
                        ['email' => $memberEmail],
                        [
                            'name' => "Thành viên - " . explode('@', $memberEmail)[0],
                            'phone' => '',
                            'address' => '',
                            'notes' => 'Tự động tạo cho family account'
                        ]
                    );
                    
                    $member = FamilyMember::create([
                        'family_account_id' => $familyAccount->id,
                        'customer_id' => $memberCustomer->id,
                        'member_email' => $memberEmail,
                        'member_role' => 'member',
                        'status' => 'active',
                        'start_date' => null, // Để trống như yêu cầu
                        'end_date' => null, // Để trống như yêu cầu
                        'member_notes' => 'Thành viên gia đình',
                        'internal_notes' => 'Được tạo tự động từ command',
                        'added_by' => null
                    ]);
                    
                    $createdMembers++;
                    $this->line("    ✓ Tạo member: {$memberEmail}");
                }
                
                // Cập nhật số lượng thành viên
                $familyAccount->updateMemberCount();
                $this->line("  ✓ Cập nhật số lượng thành viên: {$familyAccount->fresh()->current_members}");
                $this->newLine();
            }
            
            DB::commit();
            
            $this->info('=== HOÀN THÀNH ===');
            $this->info("✓ Đã tạo {$createdFamilies} family accounts");
            $this->info("✓ Đã tạo {$createdMembers} family members");
            $this->info('✓ Tất cả dữ liệu đã được lưu thành công!');
            $this->newLine();
            $this->info('Bạn có thể truy cập trang quản lý tại: http://truycuuthongtin.test:8080/admin/family-accounts');
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ LỖI: " . $e->getMessage());
            $this->error("Đã rollback tất cả thay đổi.");
            return 1;
        }
    }
}
