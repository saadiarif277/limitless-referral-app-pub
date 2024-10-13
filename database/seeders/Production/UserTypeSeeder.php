<?php

namespace Database\Seeders\Production;

use App\Models\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Guest'],
            ['name' => 'System'],
            ['name' => 'Administrator'],
            ['name' => 'Attorney'],
            ['name' => 'Doctor'],
            ['name' => 'Patient'],
            ['name' => 'Finance'],
            ['name' => 'Provider'],
            ['name' => 'Staff'],
        ])->each(function ($userType) {
            UserType::firstOrCreate([
                'name' => $userType['name'],
            ], array_merge($userType, [
                // ...
            ]));
        });
    }
}
