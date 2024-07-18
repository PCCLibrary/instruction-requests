<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Subfission\Cas\Facades\Cas;
use App\Models\User;

/**
 * Middleware to handle CAS authentication and check user existence in the database.
 *
 * This middleware ensures that the user is authenticated through CAS and then verifies
 * whether the authenticated user exists in the local database. If the user does not exist,
 * it redirects to the homepage with an error message. If any part of the authentication
 * process fails, it catches the exception and logs it for further diagnosis.
 */
class CasAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        Log::info('Entering CAS authentication middleware.');

        try {
            Log::info('Trying cas');

            if (!Cas::checkAuthentication()) {
                Log::info('User not authenticated, attempting to authenticate via CAS.');
                Cas::authenticate(); // This call should redirect and not return unless there's an issue.
                Log::error('CAS::authenticate did not redirect as expected. Continuing in middleware which is unexpected.');
            }
        } catch (\Exception $e) {
            Log::error('Exception during CAS authentication: ' . $e->getMessage());
            // Optionally redirect to a custom error page or handle the exception gracefully.
            return redirect('/error')->withErrors('Authentication error: ' . $e->getMessage());
        }

        // Retrieve the username from CAS
        $casUser = Cas::user();
        Log::info('User authenticated with CAS.', ['user' => $casUser]);

        // Verify if the authenticated CAS user exists in the local database
        $user = User::where('name', $casUser)->first();
        if (!$user) {
            Log::warning('Authenticated user not found in local database.', ['casUser' => $casUser]);
            return redirect('/')->withErrors('Unauthorized access - user does not exist.');
        }

        // Optionally set the user in the session or Auth context if needed
        session()->put('cas_user', $casUser);
        Log::info('CAS user verified and session updated.', ['cas_user' => $casUser]);

        return $next($request);
    }
}
