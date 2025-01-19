<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicInstructionRequestController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InstructionRequestController;
use App\Http\Controllers\InstructionRequestDetailsController;
use App\Http\Controllers\ClassesController;
use Livewire\Livewire;

    ///*
    //|--------------------------------------------------------------------------
    //| Livewire Routes Configuration
    //|--------------------------------------------------------------------------
    //|
    //| These routes configure Livewire to work correctly in the subdirectory setup.
    //|
    //*/
    //
    //Livewire::setScriptRoute(function($handle) {
    //    return Route::get('/library/instruction-requests/public/vendor/livewire/livewire.js', $handle);
    //});
    //
    //Livewire::setUpdateRoute(function($handle) {
    //    return Route::post('/library/instruction-requests/public/livewire/update', $handle)
    //        ->middleware('web');
    //});
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| These routes are publicly accessible and do not require authentication.
|
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
|
| Laravel authentication routes.
|
*/
Auth::routes();

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Dashboard)
|--------------------------------------------------------------------------
|
| These routes require the user to be authenticated and are prefixed with "/dashboard".
|
*/
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    // Debug route
    Route::get('debug-assets', function () {
        return view('debug-assets');
    });

    // Dashboard home
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');

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
    Route::get('instructionRequests/{id}/edit', [InstructionRequestController::class, 'edit'])
        ->name('instructionRequests.edit');
    Route::get('instructionRequests/{id}/copy', [InstructionRequestController::class, 'copy'])
        ->name('instructionRequests.copy');
    Route::post('instructionRequests/{id}/accept', [InstructionRequestController::class, 'accept'])
        ->name('instructionRequests.accept');
    Route::post('instructionRequests/{id}/reject', [InstructionRequestController::class, 'reject'])
        ->name('instructionRequests.reject');
});
