<?php

namespace App\Listeners;

use Slides\Saml2\Events\SignedIn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class Saml2LoginListener
{
    /**
     * Handle the event.
     *
     * @param  SignedIn  $event
     * @return void
     */
    public function handle(SignedIn $event)
    {
        $samlUser = $event->getSaml2User();
        $attributes = $samlUser->getAttributes();

        // Log the raw attributes from the SAML server
        Log::info('SAML attributes received', $attributes);

        // Extract the email and name attributes
        $userEmail = $attributes['email'][0] ?? null;
        $userName = $attributes['name'][0] ?? null;

        // Log the extracted email and name
        Log::info('Extracted SAML attributes', ['email' => $userEmail, 'name' => $userName]);

        // Check if the user exists in the database
        $user = User::where('email', $userEmail)->first();

        if ($user && $user->name === $userName) {
            // Log the successful authentication
            Log::info('User authenticated successfully', ['email' => $userEmail, 'name' => $userName]);

            // Log the user in
            Auth::login($user);
        } else {
            // Log the failure to find or match the user
            Log::warning('User not found or details do not match', ['email' => $userEmail, 'name' => $userName]);

            // Optionally, handle the case where the user does not exist or does not match
            abort(403, 'User not found or details do not match');
        }
    }
}
