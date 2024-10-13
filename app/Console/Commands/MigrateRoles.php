<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Console\Command;

class MigrateRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        collect([
            ['role' => 'Administrator',   'user_type_id' => UserType::ADMINISTRATOR],
            ['role' => 'Attorney',        'user_type_id' => UserType::ATTORNEY],
            ['role' => 'Doctor',          'user_type_id' => UserType::DOCTOR],
            ['role' => 'Funding Company', 'user_type_id' => UserType::FINANCE],
            ['role' => 'Office Manager',  'user_type_id' => UserType::PROVIDER],
            ['role' => 'Patient',         'user_type_id' => UserType::PATIENT],
            ['role' => 'System',          'user_type_id' => UserType::SYSTEM],
        ])->each(function ($item) {
            $users = User::query()
                ->role($item['role']);

            $this->info("Found {$users->count()} users for {$item['role']}.");

            User::query()
                ->whereIn('user_id', $users->pluck('user_id'))
                ->update(['user_type_id' => $item['user_type_id']]);
        });
    }
}
