<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/token/generate',
        'api/media/upload',
        'api/media/delete/*',
        'api/new-request',
        'dashboard/instructionRequests/*/refresh-lock',
        'dashboard/instructionRequests/*/release-lock'
    ];
}
