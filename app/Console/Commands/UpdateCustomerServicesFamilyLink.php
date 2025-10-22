<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FamilyMember;
use App\Models\CustomerService;
use App\Models\ServicePackage;

class UpdateCustomerServicesFamilyLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'family:link-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link customer services to family accounts based on family members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating customer services family links...');

        // Lấy tất cả family members
        $familyMembers = FamilyMember::with(['customer', 'familyAccount'])->get();

        $updatedCount = 0;

        foreach ($familyMembers as $member) {
            if (!$member->customer) {
                continue;
            }

            // Tìm customer services của member này với add family package
            $services = CustomerService::where('customer_id', $member->customer_id)
                ->whereHas('servicePackage', function ($query) {
                    $query->where('account_type', 'Tài khoản add family');
                })
                ->whereNull('family_account_id') // Chỉ cập nhật những service chưa có family_account_id
                ->get();

            foreach ($services as $service) {
                $service->family_account_id = $member->family_account_id;
                $service->save();
                $updatedCount++;

                $this->line("Updated service {$service->id} for customer {$member->customer->name} -> Family {$member->family_account_id}");
            }
        }

        $this->info("Updated {$updatedCount} customer services with family account links.");
        return 0;
    }
}
