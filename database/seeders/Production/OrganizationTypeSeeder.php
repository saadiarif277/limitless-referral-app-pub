<?php

namespace Database\Seeders\Production;

use App\Models\OrganizationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'System',                     'description' => 'The organization type for the system. Only administrators and system users are allowed to be members.'],
            ['name' => 'Legal',                      'description' => ''],
            ['name' => 'Funding',                    'description' => ''],
            ['name' => 'Clinics & Medical Offices',  'description' => ''],
            ['name' => 'Ambulatory Surgical Center', 'description' => ''],
            ['name' => 'Hospital',                   'description' => ''],
            ['name' => 'Imaging & Radiology Center', 'description' => ''],
            ['name' => 'Medical laboratory',         'description' => ''],
        ])->each(function ($organizationType) {
            OrganizationType::firstOrCreate([
                'slug' => Str::slug($organizationType['name'])
            ], array_merge($organizationType, [
                'slug' => Str::slug($organizationType['name'])
            ]));
        });
    }
}
