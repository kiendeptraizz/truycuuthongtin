<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password {email} {password}';
    protected $description = 'Reset admin password';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            $this->error('Admin not found!');
            return 1;
        }

        $admin->password = Hash::make($password);
        $admin->save();

        $this->info('Password updated successfully!');
        return 0;
    }
}
