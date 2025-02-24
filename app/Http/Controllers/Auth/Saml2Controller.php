<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Slides\Saml2\Auth as Saml2Auth;

class Saml2Controller extends Controller
{
    protected $auth;

    public function __construct(Saml2Auth $auth)
    {
        $this->auth = $auth;
    }

    public function login()
    {
        return $this->auth->login();
    }

    public function acs()
    {
        $errors = $this->auth->acs();

        if (!empty($errors)) {
            return redirect()->route('login')
                ->withErrors(['saml' => $this->auth->getLastErrorReason()]);
        }

        if (!$this->auth->isAuthenticated()) {
            return redirect()->route('login')
                ->withErrors(['saml' => 'Could not authenticate']);
        }

        $user = $this->auth->getSaml2User();
        $attributes = $user->getAttributes();

        // We'll update these attribute keys once we get them from the admin
        $email = $attributes['email'][0] ?? null;
        $name = $attributes['name'][0] ?? null;

        if (!$email || !$name) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Invalid SAML response']);
        }

        $user = User::where('email', $email)
            ->where('name', $name)
            ->first();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'User not found']);
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->auth->logout();
    }

    public function sls(Request $request)
    {
        $errors = $this->auth->sls();

        if (!empty($errors)) {
            logger()->error('SAML SLS error: ' . implode(', ', $errors));
        }

        return redirect('/');
    }

    public function metadata()
    {
        try {
            $metadata = $this->auth->getMetadata();
            return response($metadata, 200, ['Content-Type' => 'text/xml']);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }
}
