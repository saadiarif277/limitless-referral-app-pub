<?php

namespace Database\Seeders\Production;

use App\Models\ReferralType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $referralTypes = collect([
            ['name' => 'Other',    'description' => '', 'is_generated' => true],
            ['name' => 'Surgical', 'description' => '', 'is_generated' => true],
            ['name' => 'Clinical', 'description' => '', 'is_generated' => true],
        ])->each(function ($referralType) {
            ReferralType::firstOrCreate([
                'name' => $referralType['name'],
            ], array_merge($referralType, [
                // ...
            ]));
        });
    }
}
