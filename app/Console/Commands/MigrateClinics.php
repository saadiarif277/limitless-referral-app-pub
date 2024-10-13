<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Console\Command;
use DB;

class MigrateClinics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-clinics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates the old clinics table for use in the new organizations table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clinics = DB::table('clinics')->get();

        $clinics->each(function ($clinic) {
            $this->info("Migrating Clinic#{$clinic->clinic_id}: {$clinic->name}.");

            $organization = Organization::updateOrCreate([
                'name' => $clinic->name,
            ], [
                'slug' => str()->slug($clinic->name),
                'email' => $clinic->email,
                'description' => 'Migrated from Clinic.',
                'phone_number' => $clinic->phone_number,
                'address_line_1' => $clinic->address_line_1,
                'address_line_2' => $clinic->address_line_2,
                'city' => $clinic->city,
                'state_id' => $clinic->state_id,
                'zip_code' => $clinic->zip_code,
                'organization_type_id' => OrganizationType::CLINICS_MEDICAL_OFFICES,
            ]);

            DB::table('pivot_clinics_users')
                ->where('clinic_id', $clinic->clinic_id)
                ->get()
                ->each(function ($pivot) use ($organization, $clinic) {
                    $user = User::find($pivot->user_id);

                    if (!$user) {
                        $this->info("User#{$pivot->user_id} could not be found, skipping.");
                        return;
                    }

                    $this->info("Assigning User#{$user->getKey()}: {$user->name} to Organization#{$organization->getKey()}: {$organization->name}.");
                    $user->organizations()->syncWithoutDetaching($organization->getKey());

                    $referrals = Referral::query()
                        ->with('appointment')
                        ->where('clinic_id', $clinic->clinic_id)
                        ->whereHas('appointment')
                        ->get()
                        ->each(function ($referral) use ($organization) {
                            $appointment = $referral->appointment;

                            $this->info("Assigning Appointment#{$appointment->getKey()} to Organization#{$organization->getKey()}: {$organization->name}.");

                            $appointment->update([
                                'organization_id' => $organization->getKey(),
                            ]);
                        });
                });
        });
    }
}
