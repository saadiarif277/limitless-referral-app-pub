<?php

namespace App\Console\Commands;

use App\Models\Referral;
use Illuminate\Console\Command;

class MigrateReferrals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-referrals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates the old referrals for use in the new structure for referrals.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Referral::query()
            ->whereNotNull('doctor_user_id')
            ->get()
            ->each(function ($referral) {
                $this->info("Assigning Referral User#{$referral->doctor_user_id}: to Recipient User.");
                $referral->update([ 'recipient_user_id' => $referral->doctor_user_id ]);
            });

        Referral::query()
            ->whereNull('procedure_id')
            ->get()
            ->each(function ($referral) {
                $this->info("Assigning default procedure.");
                $referral->update([ 'procedure_id' => 1 ]);
            });
    }
}
