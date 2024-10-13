<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::permanentRedirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Panel Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the authenticated area
| of the application or the panel. These are further grouped into the
| admin and user panels.
|
*/
Route::group(['middleware' => ['auth']], function() {
    
    Route::impersonate();

    /*
    |--------------------------------------------------------------------------
    | Panel Routes
    |--------------------------------------------------------------------------
    |
    | The routes found here are for authenticated and authorized users.
    |
    */
    Route::group(['as' => 'panel.'], function() {

        Route::get('dashboard', [\App\Http\Controllers\Panel\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('activity-log', [\App\Http\Controllers\Panel\ActivityLogController::class, 'index'])
            ->name('activity-log.index');

        Route::get('documents/{document}/download', [\App\Http\Controllers\Panel\DocumentController::class, 'download'])
            ->name('documents.download');

        Route::delete('documents/{document}', [\App\Http\Controllers\Panel\DocumentController::class, 'destroy'])
            ->name('documents.destroy');

        Route::get('referrals/{referral}/documents/referral-summary', [\App\Http\Controllers\Panel\ReferralController::class, 'getSummaryDocument'])
            ->name('referrals.documents.summary');

        Route::get('/me/profile', [\App\Http\Controllers\Panel\ProfileController::class, 'edit'])
            ->name('me.profile.edit');

        Route::patch('/me/profile', [\App\Http\Controllers\Panel\ProfileController::class, 'update'])
            ->name('me.profile.update');

        Route::resources([
            'appointments' => \App\Http\Controllers\Panel\AppointmentController::class,
            'attorneys' => \App\Http\Controllers\Panel\AttorneyController::class,
            'clinics' => \App\Http\Controllers\Panel\ClinicController::class,
            'doctors' => \App\Http\Controllers\Panel\DoctorController::class,
            'document-categories' => \App\Http\Controllers\Panel\DocumentCategoryController::class,
            'document-types' => \App\Http\Controllers\Panel\DocumentTypeController::class,
            'law-firms' => \App\Http\Controllers\Panel\LawFirmController::class,
            'organizations' => \App\Http\Controllers\Panel\OrganizationController::class,
            'patients' => \App\Http\Controllers\Panel\PatientController::class,
            'referral-types' => \App\Http\Controllers\Panel\ReferralTypeController::class,
            'referrals' => \App\Http\Controllers\Panel\ReferralController::class,
            'roles' => \App\Http\Controllers\Panel\RoleController::class,
            'states' => \App\Http\Controllers\Panel\StateController::class,
            'users' => \App\Http\Controllers\Panel\UserController::class,
        ]);

    });

});

require __DIR__.'/auth.php';
