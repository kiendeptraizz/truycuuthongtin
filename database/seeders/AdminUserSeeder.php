<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Tạo tài khoản admin mặc định
     */
    public function run(): void
    {
        // Tạo admin user nếu chưa tồn tại
        User::firstOrCreate(
            ['email' => 'admin@truycuuthongtin.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123456'),
            ]
        );

        $this->command->info('✅ Admin user created successfully!');
        $this->command->info('📧 Email: admin@truycuuthongtin.com');
        $this->command->info('🔑 Password: admin123456');
        $this->command->warn('⚠️  Hãy đổi mật khẩu ngay sau khi đăng nhập lần đầu!');
    }
}

