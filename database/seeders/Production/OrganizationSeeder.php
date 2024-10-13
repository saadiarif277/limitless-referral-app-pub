<?php

namespace Database\Seeders\Production;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'System Organization', 'organization_type_id' => 1],
        ])->each(function ($organization) {
            Organization::firstOrCreate([
                'slug' => Str::slug($organization['name']),
            ], array_merge($organization, [
                // ...
            ]));
        });
    }
}
