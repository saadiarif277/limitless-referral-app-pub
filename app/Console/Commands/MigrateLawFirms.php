<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\User;
use Illuminate\Console\Command;
use DB;

class MigrateLawFirms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-law-firms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates the old law firms table for use in the new organizations table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lawFirms = DB::table('law_firms')->get();

        $lawFirms->each(function ($lawFirm) {
            $this->info("Migrating LawFirm#{$lawFirm->law_firm_id}: {$lawFirm->name}.");

            $organization = Organization::updateOrCreate([
                'name' => $lawFirm->name,
            ], [
                'slug' => str()->slug($lawFirm->name),
                'email' => $lawFirm->email,
                'description' => 'Migrated from Law Firm.',
                'phone_number' => $lawFirm->phone_number,
                'address_line_1' => $lawFirm->address_line_1,
                'address_line_2' => $lawFirm->address_line_2,
                'city' => $lawFirm->city,
                'state_id' => $lawFirm->state_id,
                'zip_code' => $lawFirm->zip_code,
                'organization_type_id' => OrganizationType::LEGAL,
            ]);

            User::query()
                ->where('law_firm_id', $lawFirm->law_firm_id)
                ->get()
                ->each(function ($user) use ($organization) {
                    $this->info("Assigning User#{$user->getKey()}: {$user->name} to Organization#{$organization->getKey()}: {$organization->name}.");
                    $user->organizations()->syncWithoutDetaching($organization->getKey());
                });
        });
    }
}
