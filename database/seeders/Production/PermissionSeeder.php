<?php

namespace Database\Seeders\Production;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelFiles = File::allFiles(app_path('Models'));
        $modelActions = ['list-own', 'list', 'show-own', 'show', 'store', 'update', 'destroy'];

        $models = collect($modelFiles)->map(function ($file) {
            return Str::before($file->getFilename(), '.php');
        });

        $models->each(function ($model) use ($modelActions) {
            $modelName = Str::slug(Str::kebab($model)); // The slugified name of the model.

            foreach($modelActions as $modelAction) {
                $permissionName = "{$modelName}.${modelAction}";
                $permission = [
                    'name' => $permissionName,
                ];

                Permission::firstOrCreate($permission, array_merge($permission, [
                    // ...
                ]));
            }
        });

        /**
         * Generate Secondary Permissions.
         */
        $permissions = collect([
            'app.panel.admin',
            'app.stats.user-role-population.list',
            'app.stats.user-role-population.list-own',
            'app.stats.referral-status-distribution.list',
            'app.stats.referral-status-distribution.list-own',
            'app.stats.top-clinics-by-referrals.list',
            'app.stats.top-clinics-by-referrals.list-own',
        ])->each(function ($permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ], [
                // ...
            ]);
        });

        /**
         * Generate Tertiary Permissions.
         */
        $tertiaryPermissions = [
            'activity-log',
        ];

        foreach($tertiaryPermissions as $tertiaryPermission) {
            foreach($modelActions as $modelAction) {
                Permission::firstOrCreate([
                    'name' => "{$tertiaryPermission}.{$modelAction}",
                ], [
                    // ...
                ]);
            }
        }

        /**
         * Generate Document Type Permissions
         */
        DocumentType::all()
            ->each(function ($documentType) {
                $documentType->update(['updated_at' => now()]);
            });
    }
}
