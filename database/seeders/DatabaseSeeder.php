<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 000
            Production\AppointmentTypeSeeder::class,
            Production\DocumentCategorySeeder::class,
            Production\PermissionSeeder::class,
            Production\ReferralReasonSeeder::class,
            Production\ReferralStatusSeeder::class,
            
            // 100
            Production\DocumentTypeSeeder::class,
            Production\RoleSeeder::class,

            // 200
            Production\StateSeeder::class,
            
            // 300
            Production\UserSeeder::class,

            // 400
            Production\MedicalSpecialtySeeder::class,

            // 500
            Production\ReferralTypeSeeder::class,
            Production\UserTypeSeeder::class,

            // 600
            Production\OrganizationTypeSeeder::class,
            Production\OrganizationSeeder::class,
            Production\ProcedureSeeder::class,
        ]);

        if (!app()->environment(['production', 'staging'])) {
            // \App\Models\Organization::factory(50)->create();
        }
    }
}
