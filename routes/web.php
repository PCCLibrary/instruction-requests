<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicInstructionRequestController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InstructionRequestController;
use App\Http\Controllers\InstructionRequestDetailsController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\Auth\SamlAuthController;
use App\Http\Controllers\MediaController;

use Livewire\Livewire;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

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

// Dropzone test route (development only)
if (!app()->environment('production')) {
    Route::get('/test/dropzone', function () {
        return view('test.dropzone-test');
    })->name('test.dropzone');
}

// Public form for creating instruction requests
Route::get('/', [PublicInstructionRequestController::class, 'create'])
    ->name('public.instruction-request.create');

// Store the submitted instruction request from the public form
Route::post('/instruction-requests', [PublicInstructionRequestController::class, 'store'])
    ->name('public.instruction-request.store');


/*
|--------------------------------------------------------------------------
| File Upload Routes
|--------------------------------------------------------------------------
| These routes handle file uploads for both public and authenticated users.
| They are exempt from CSRF verification in the VerifyCsrfToken middleware.
*/
// Public file upload routes
Route::post('/api/token/generate', [MediaController::class, 'generateUploadToken'])
    ->name('media.token.generate')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/api/media/upload', [MediaController::class, 'publicUpload'])
    ->name('media.upload.public')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::delete('/api/media/delete/{id}', [MediaController::class, 'publicDelete'])
    ->name('media.delete.public')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Standard Laravel authentication routes
Auth::routes();

/*
|--------------------------------------------------------------------------
| SAML2 Authentication Routes
|--------------------------------------------------------------------------
|
| These routes handle the SAML2 authentication flow with PCC's identity provider.
| - login: Initiates the SAML2 authentication flow
| - acs: Processes the SAML response from the IdP (Assertion Consumer Service)
| - logout: Handles local logout
| - metadata: Provides SP metadata for IdP configuration
|
*/
Route::prefix('saml2')->middleware('guest')->group(function () {
    // Initiates the SAML2 authentication flow by redirecting to the IdP
    Route::get('login', [SamlAuthController::class, 'login'])
        ->name('saml2.login');


    // Processes the SAML2 assertion response from the IdP (Assertion Consumer Service)
    // Accept both POST and GET methods to handle different binding types
    Route::match(['get', 'post'], 'acs', [SamlAuthController::class, 'handleCallback'])
        ->name('saml2.acs')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    // Performs a local logout
    Route::get('logout', [SamlAuthController::class, 'logout'])
        ->name('saml2.logout');

    // Returns the SAML2 Service Provider metadata as XML
    Route::get('metadata', [SamlAuthController::class, 'metadata'])
        ->name('saml2.metadata');
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

    // Temporary mockup routes for new dashboard designs
    Route::get('/mockup-librarian', function () {
        return view('mockups.librarian-dashboard');
    })->name('dashboard.mockup.librarian');

    Route::get('/mockup-scheduler', function () {
        return view('mockups.scheduler-dashboard');
    })->name('dashboard.mockup.scheduler');

    // Statistics landing page
    Route::get('/statistics', [App\Http\Controllers\StatisticsController::class, 'index'])->name('statistics.index');

    // Statistics report pages
    Route::get('/statistics/trends', [App\Http\Controllers\StatisticsController::class, 'trends'])->name('statistics.trends');
    Route::get('/statistics/detailed-export', [App\Http\Controllers\StatisticsController::class, 'detailedExport'])->name('statistics.detailed-export');
    Route::get('/statistics/comparison', [App\Http\Controllers\StatisticsController::class, 'comparison'])->name('statistics.comparison');

    /*
    |--------------------------------------------------------------------------
    | Resource Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('instructors', InstructorController::class);
    Route::resource('campuses', CampusController::class);
    Route::get('campuses/{id}/delete-impact', [CampusController::class, 'getDeleteImpact'])
        ->name('campuses.deleteImpact');
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
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/toggle-assignment', [AdminController::class, 'toggleAssignmentAvailability'])->name('toggle-assignment');

        // System management routes
        Route::get('/system-status', [AdminController::class, 'systemStatus'])->name('system-status');
        Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear-cache');
        Route::post('/flush-queue', [AdminController::class, 'flushQueue'])->name('flush-queue');
        Route::post('/restart-queue', [AdminController::class, 'restartQueue'])->name('restart-queue');
    });

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
    Route::delete('instructionRequests/{id}/calendar-event', [InstructionRequestController::class, 'deleteCalendarEvent'])
        ->name('instructionRequests.deleteCalendarEvent');

    /*
    |--------------------------------------------------------------------------
    | Lock Management Routes
    |--------------------------------------------------------------------------
    */
    Route::get('instructionRequests/{id}/unlock', [InstructionRequestController::class, 'unlock'])
        ->name('instructionRequests.unlock');
    Route::get('instructionRequests/{id}/cancel-unlock', [InstructionRequestController::class, 'cancelUnlock'])
        ->name('instructionRequests.cancelUnlock');
    Route::post('instructionRequests/{id}/refresh-lock', [InstructionRequestController::class, 'refreshLock'])
        ->name('instructionRequests.refreshLock');
    Route::post('instructionRequests/{id}/release-lock', [InstructionRequestController::class, 'releaseLock'])
        ->name('instructionRequests.releaseLock');

    /*
    |--------------------------------------------------------------------------
    | Admin File Upload Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/media/upload', [MediaController::class, 'upload'])
        ->name('media.upload');
    Route::delete('/media/delete/{id}', [MediaController::class, 'delete'])
        ->name('media.delete');
});

// Add this for debugging during development only (remove in production)
Route::get('debug-user', function () {
    $user = Auth::user();
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'is_admin' => $user->is_admin,
        'available_for_assignment' => $user->available_for_assignment ?? 'field_not_set',
        'admin_route_exists' => Route::has('admin.index'),
        'admin_route_url' => Route::has('admin.index') ? route('admin.index') : 'route_not_found'
    ];
})->middleware('auth')->name('debug.user');

// Route::get('debug', function () {
//     dd(Socialite::driver('saml2')->user());
// })->name('saml2.debug');

// Environment test route (not available in production)
if (!app()->environment('production')) {
    Route::get('/env-test', function () {
        return [
            'environment' => app()->environment(),
            'app_url' => config('app.url'),
            'filesystem_driver' => config('filesystems.default'),
            'media_disk' => config('media-library.disk_name'),
            'public_disk_root' => config('filesystems.disks.public.root'),
            'public_disk_url' => config('filesystems.disks.public.url'),
            'storage_link_exists' => file_exists(public_path('storage')),
            'upload_path_exists' => Storage::disk(config('media-library.disk_name'))->exists('uploads/temp'),
            'php_version' => PHP_VERSION,
            'server_time' => now()->toDateTimeString(),
            'timezone' => config('app.timezone')
        ];
    })->name('env.test');

}
