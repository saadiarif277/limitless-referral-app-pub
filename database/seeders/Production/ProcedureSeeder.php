<?php

namespace Database\Seeders\Production;

use App\Models\Procedure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProcedureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Unkown Procedure', 'description' => '', 'code' => '00000'],
        ])->each(function ($procedure) {
            Procedure::firstOrCreate([
                'code' => $procedure['code'],
            ], array_merge($procedure, [
                // ...
            ]));
        });
    }
}
