<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsMerchant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isMerchant()) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
