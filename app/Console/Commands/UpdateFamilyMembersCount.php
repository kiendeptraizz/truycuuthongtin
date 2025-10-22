<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FamilyAccount;

class UpdateFamilyMembersCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'family:update-members-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update current_members count for all family accounts based on active members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating family members count...');

        $families = FamilyAccount::with('members')->get();
        $updated = 0;

        foreach ($families as $family) {
            $actualCount = $family->members()->where('status', 'active')->count();
            $oldCount = $family->getOriginal('current_members');

            if ($actualCount != $oldCount) {
                $family->current_members = $actualCount;
                $family->save();
                $updated++;

                $this->line("Updated {$family->family_name}: {$oldCount} -> {$actualCount}");
            }
        }

        $this->info("Updated {$updated} family accounts out of {$families->count()} total.");

        return Command::SUCCESS;
    }
}
