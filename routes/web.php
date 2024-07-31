<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicInstructionRequestController;
//use App\Http\Controllers\UploadController;
use Slides\Saml2\Http\Controllers\Saml2Controller;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| These routes are publicly accessible and do not require authentication.
|
*/

// Database connection test route.
Route::get('/db-test', function () {
    try {
        $databaseName = DB::connection()->getDatabaseName();
        return "Connected to the '$databaseName' database successfully.";
    } catch (\Exception $e) {
        return 'Database connection failed: ' . $e->getMessage();
    }
});

// Index page with the public form to create instruction requests.
Route::get('/', [PublicInstructionRequestController::class, 'create'])->name('public.instruction-request.create');

// Store the submitted instruction request from the public form.
Route::post('instruction-requests', [PublicInstructionRequestController::class, 'store'])->name('public.instruction-request.store');

/*
|--------------------------------------------------------------------------
| SAML Authentication Routes
|--------------------------------------------------------------------------
|
| Routes for SAML authentication using 24Slides\laravel-saml2.
|
*/

// Initiate the SAML login
Route::get('/saml2/login', [Saml2Controller::class, 'login'])->name('saml2.login');

// SAML logout
Route::get('/saml2/logout', [Saml2Controller::class, 'logout'])->name('saml2.logout');

// SAML ACS endpoint
Route::post('/saml2/acs', [Saml2Controller::class, 'acs'])->name('saml2.acs');

// SAML SLS endpoint
Route::get('/saml2/sls', [Saml2Controller::class, 'sls'])->name('saml2.sls');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Routes for user authentication. This is handled by Laravel's Auth::routes().
|
*/

Auth::routes();

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| Routes that require the user to be authenticated. They are within the "auth" middleware.
|
*/

// Home/dashboard page for authenticated users.
Route::get('/dashboard', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Resource route for managing instructors (accessible only by authenticated users).
Route::resource('instructors', App\Http\Controllers\InstructorController::class)->middleware('auth');

// Resource route for managing campuses (accessible only by authenticated users).
Route::resource('campuses', App\Http\Controllers\CampusController::class)->middleware('auth');

// Resource route for managing users (accessible only by authenticated users).
Route::resource('users', App\Http\Controllers\UserController::class)->middleware('auth');

// Resource route for managing instruction requests (accessible only by authenticated users).
Route::resource('instructionRequests', App\Http\Controllers\InstructionRequestController::class)->middleware('auth');

// Resource route for managing instruction request details (accessible only by authenticated users).
Route::resource('instructionRequestDetails', App\Http\Controllers\InstructionRequestDetailsController::class)->middleware('auth');

// Edit instruction requests
Route::get('instructionRequests/{id}/edit', [App\Http\Controllers\InstructionRequestController::class, 'copy'])->name('instructionRequests.edit')->middleware('auth');

// Copy instruction requests
Route::get('instructionRequests/{id}/copy', [App\Http\Controllers\InstructionRequestController::class, 'copy'])->name('instructionRequests.copy')->middleware('auth');

// Accept instruction request
Route::post('/instructionRequests/{id}/accept', [App\Http\Controllers\InstructionRequestController::class, 'accept'])->name('instructionRequests.accept')->middleware('auth');

// Reject instruction request
Route::post('/instructionRequests/{id}/reject', [App\Http\Controllers\InstructionRequestController::class, 'reject'])->name('instructionRequests.reject')->middleware('auth');

// Resource route for managing classes
Route::resource('classes', App\Http\Controllers\ClassesController::class)->middleware('auth');

/*
|--------------------------------------------------------------------------
| InfyOm Generator Builder Routes
|--------------------------------------------------------------------------
|
| Routes for the InfyOm Generator Builder which is a tool to generate files
| like models, controllers, views, etc., for your CRUD applications.
|
*/

Route::group(['middleware' => 'auth'], function () {
    Route::get('generator_builder', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@builder')->name('io_generator_builder');
    Route::get('field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@fieldTemplate')->name('io_field_template');
    Route::get('relation_field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@relationFieldTemplate')->name('io_relation_field_template');
    Route::post('generator_builder/generate', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generate')->name('io_generator_builder_generate');
    Route::post('generator_builder/rollback', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@rollback')->name('io_generator_builder_rollback');
    Route::post('generator_builder/generate-from-file', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generateFromFile')->name('io_generator_builder_generate_from_file');
});
