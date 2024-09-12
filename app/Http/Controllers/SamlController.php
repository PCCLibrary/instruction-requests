<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use OneLogin\Saml2\Auth;

class SAMLController extends Controller
{
    /**
     * Initiates the SAML login process.
     */
    public function login(Request $request)
    {
        $auth = new Auth(config('php-saml'));
        $redirectUrl = $auth->login(null, [], false, false, true);
        $request->session()->put('requestId', $auth->getLastRequestID());
        return redirect($redirectUrl);
    }

    /**
     * Handles the SAML response (ACS).
     */
    public function acs(Request $request)
    {
        $auth = new Auth(config('php-saml'));
        $auth->processResponse($request->session()->get('requestId'));

        // Log any SAML errors for debugging
        if (count($auth->getErrors()) > 0 || !$auth->isAuthenticated()) {
            \Log::error('SAML Errors', ['errors' => $auth->getErrors()]);
            return 'An error occurred processing SAML response';
        }

        // Find user by email in the local database
        $user = User::query()->where('email', $auth->getNameId())->first();
        if (!$user) {
            \Log::warning('SAML user not found', ['email' => $auth->getNameId()]);
            return 'User not found.';
        }

        // Log in the user
        auth()->login($user);

        return redirect('/dashboard');
    }
}
