<?php
namespace App\Auth\Guards;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Models\User; // Adjust namespace as needed for your User model

class CasGuard implements Guard
{
    protected $user;

    public function check()
    {
        return cas()->isAuthenticated();
    }

    public function guest()
    {
        return !cas()->isAuthenticated();
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        if (cas()->isAuthenticated()) {
            $userIdentifier = cas()->user();
            // Retrieve user from your database, create if doesn't exist
            $user = User::firstOrCreate(['name' => $userIdentifier], [
                // Assuming 'name' is the CAS identifier, adjust as needed
                // Add any default values for new users as needed
            ]);

            $this->user = $user;
            return $this->user;
        }

        return null;
    }

    public function id()
    {
        if ($user = $this->user()) {
            return $user->getAuthIdentifier();
        }
        return null;
    }

    public function validate(array $credentials = [])
    {
        // You might not need to implement this method for CAS authentication
        throw new AuthenticationException('Direct validation not supported for CAS authentication.');
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }
}
