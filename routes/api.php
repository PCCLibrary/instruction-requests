<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CampusController;
use App\Http\Controllers\API\LibrarianController;
use App\Http\Controllers\API\CsrfTokenController;
use Illuminate\Session\Middleware\StartSession;

use App\Http\Controllers\API\ExternalInstructionRequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes for Svelte form
Route::get('/campuses', [CampusController::class, 'index']);
Route::get('/librarians', [LibrarianController::class, 'index']);

// Route to get the CSRF token - needs session middleware using FQCN
Route::get('/get-csrf-token', [CsrfTokenController::class, 'getToken'])
    ->middleware(StartSession::class) // <-- Use the imported class constant
    ->middleware('throttle:20,1'); // Allow 20 requests per minute per IP


// External API route for Svelte form submissions
Route::post('/new-request', [ExternalInstructionRequestController::class, 'store']);
