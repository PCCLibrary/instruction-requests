<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SamlAuthController extends Controller
{
    /**
     * Redirect the user to the PCC authentication page.
     *
     * @return RedirectResponse
     */
    public function login()
    {
        Log::info('Initiating SAML2 authentication redirect');
        return Socialite::driver('saml2')->redirect();
    }

    /**
     * Handle the callback from the PCC authentication service.
     * This processes the SAML assertion and authenticates the user.
     *
     * @return RedirectResponse
     */
    public function handleCallback()
    {
        try {
            // Debug: Log the incoming request
            Log::info('SAML2 handleCallback called', [
                'request_method' => request()->method(),
                'has_saml_response' => request()->has('SAMLResponse'),
                'has_relay_state' => request()->has('RelayState'),
                'request_url' => request()->fullUrl(),
                'all_input' => request()->all()
            ]);

            // Try to get more detailed error information
            try {
                Log::info('About to call Socialite SAML2 user() method');
                $samlUser = Socialite::driver('saml2')->user();
            } catch (\Exception $e) {
                Log::error('Detailed Socialite SAML2 error', [
                    'exception_class' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'previous' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null
                ]);
                throw $e; // Re-throw to maintain existing error handling
            }

            // Log all attributes for debugging
//            Log::info('SAML2 response received', [
//                'all_attributes' => $samlUser->getRaw(),
//                'id' => $samlUser->getId(),
//                'email' => $samlUser->email,
//                'name' => $samlUser->name
//            ]);

            // Extract the user identifier - try multiple possible attributes
            $email = $samlUser->email ?? $samlUser->getRaw()['mail'][0] ?? $samlUser->getRaw()['emailAddress'][0] ?? null;
            $name = $samlUser->name ?? $samlUser->getRaw()['displayName'][0] ?? $samlUser->getRaw()['cn'][0] ?? null;

            // If email is still null, try to use the NameID if it looks like an email
            if (!$email && filter_var($samlUser->getId(), FILTER_VALIDATE_EMAIL)) {
                $email = $samlUser->getId();
            }

            // If name is still null, use the UID or NameID as a fallback
            if (!$name) {
                $name = $samlUser->getRaw()['uid'][0] ?? $samlUser->getId() ?? 'Unknown User';
            }

            // Validate required fields
            if (!$email) {
                Log::error('SAML2 response missing required attributes', [
                    'email_present' => (bool)$email,
                    'name_present' => (bool)$name,
                    'all_attributes' => $samlUser->getRaw()
                ]);

                return redirect()->route('login')
                    ->withErrors(['email' => 'Invalid SAML response: missing required attributes']);
            }

            // Find the user by email
            $user = User::where('email', $email)->first();

            // If no matching user is found, redirect to login with error
            if (!$user) {
                Log::warning("SAML2 login failed: User with email {$email} not found");

                return redirect()->route('login')
                    ->withErrors(['email' => 'User not found']);
            }

            // Log the user in
            Auth::login($user);

//            Log::info("User {$email} successfully authenticated via SAML2");

            // Redirect to the dashboard
            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('SAML2 authentication error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // Redirect back to login with error message
            return redirect()->route('login')
                ->withErrors(['saml' => 'Authentication failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle user logout (local only).
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        $email = Auth::user() ? Auth::user()->email : 'unknown';
        Log::info("User {$email} logging out");

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Return the Service Provider metadata as XML.
     * This is used by the Identity Provider to configure the connection.
     *
     * @return \Illuminate\Http\Response
     */
    public function metadata()
    {
        try {
            Log::info('Service Provider metadata requested');

            // Log driver configuration for debugging
            $config = config('services.saml2');
            Log::debug('SAML2 configuration', [
                'config' => $config
            ]);

            // Get the metadata - this is actually a Response object
            $response = Socialite::driver('saml2')->getServiceProviderMetadata();

            // Extract the content from the Response object
            $metadata = $response->getContent();

            Log::debug('Extracted metadata content', [
                'metadata' => $metadata
            ]);

            // Return the extracted content directly
            return response($metadata, 200, ['Content-Type' => 'text/xml']);
        } catch (\Exception $e) {
            Log::error('Error generating SAML2 metadata', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Error generating SAML2 metadata: ' . $e->getMessage(), 500);
        }
    }
}
