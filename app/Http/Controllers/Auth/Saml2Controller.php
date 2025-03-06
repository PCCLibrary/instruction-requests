<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Slides\Saml2\Facades\Auth as Saml2Auth;
use Slides\Saml2\Models\Tenant;

class Saml2Controller extends Controller
{
    /**
     * Get the tenant for SAML operations
     *
     * @return Tenant
     * @throws \Exception
     */
    protected function getTenant()
    {
        // Get the first active tenant
        $tenant = Tenant::first();

        if (!$tenant) {
            throw new \Exception('No SAML tenant configured. Run php artisan saml2:create-tenant to configure one.');
        }

        return $tenant;
    }

    /**
     * Redirect to the IdP for authentication
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    public function login()
    {
        $tenant = $this->getTenant();
        return Saml2Auth::tenantId($tenant->uuid)->login();
    }

    /**
     * Process the SAML response from the IdP (Assertion Consumer Service)
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    public function acs()
    {
        $tenant = $this->getTenant();
        $auth = Saml2Auth::tenantId($tenant->uuid);

        $errors = $auth->acs();

        if (!empty($errors)) {
            return redirect()->route('login')
                ->withErrors(['saml' => $auth->getLastErrorReason()]);
        }

        if (!$auth->isAuthenticated()) {
            return redirect()->route('login')
                ->withErrors(['saml' => 'Could not authenticate']);
        }

        $samlUser = $auth->getSaml2User();
        $attributes = $samlUser->getAttributes();

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

    /**
     * Logout the user and redirect to IdP for SLO
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $tenant = $this->getTenant();
        return Saml2Auth::tenantId($tenant->uuid)->logout();
    }

    /**
     * Process the logout response from the IdP
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function sls(Request $request)
    {
        $tenant = $this->getTenant();
        $errors = Saml2Auth::tenantId($tenant->uuid)->sls();

        if (!empty($errors)) {
            logger()->error('SAML SLS error: ' . implode(', ', $errors));
        }

        return redirect('/');
    }

    /**
     * Return the SP metadata
     *
     * @return Response
     */
    public function metadata()
    {
        try {
            $tenant = $this->getTenant();
            $metadata = Saml2Auth::tenantId($tenant->uuid)->getMetadata();
            return response($metadata, 200, ['Content-Type' => 'text/xml']);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }
}
