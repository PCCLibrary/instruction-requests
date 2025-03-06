<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicInstructionRequestController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InstructionRequestController;
use App\Http\Controllers\InstructionRequestDetailsController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\Auth\Saml2Controller;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Database connection test route
Route::get('/db-test', function () {
    try {
        $databaseName = DB::connection()->getDatabaseName();
        return "Connected to the '{$databaseName}' database successfully.";
    } catch (\Exception $e) {
        return 'Database connection failed: ' . $e->getMessage();
    }
});

// Public form for creating instruction requests
Route::get('/', [PublicInstructionRequestController::class, 'create'])
    ->name('public.instruction-request.create');

// Store the submitted instruction request from the public form
Route::post('/instruction-requests', [PublicInstructionRequestController::class, 'store'])
    ->name('public.instruction-request.store');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Standard Laravel authentication routes
Auth::routes();

// SAML2 routes
Route::prefix('saml2')->middleware('guest')->group(function () {

    // Keep these routes for backward compatibility with existing views

    Route::get('login', [Saml2Controller::class, 'login'])->name('saml2.login');
    Route::post('acs', [Saml2Controller::class, 'acs'])->name('saml2.acs');
    Route::get('logout', [Saml2Controller::class, 'logout'])->name('saml2.logout');
    Route::get('sls', [Saml2Controller::class, 'sls'])->name('saml2.sls');
    Route::get('metadata', [Saml2Controller::class, 'metadata'])->name('saml2.metadata');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Dashboard)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    // Debug route
    Route::get('debug-assets', function () {
        return view('debug-assets');
    });

    // Dashboard home
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Resource Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('instructors', InstructorController::class);
    Route::resource('campuses', CampusController::class);
    Route::resource('users', UserController::class);
    Route::resource('instructionRequests', InstructionRequestController::class);
    Route::resource('instructionRequestDetails', InstructionRequestDetailsController::class);
    Route::resource('classes', ClassesController::class);

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Instruction Requests Additional Routes
    |--------------------------------------------------------------------------
    */
    Route::get('instructionRequests/{id}/copy', [InstructionRequestController::class, 'copy'])
        ->name('instructionRequests.copy');
    Route::post('instructionRequests/{id}/accept', [InstructionRequestController::class, 'accept'])
        ->name('instructionRequests.accept');
    Route::post('instructionRequests/{id}/reject', [InstructionRequestController::class, 'reject'])
        ->name('instructionRequests.reject');

});
