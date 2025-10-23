<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AttemptAuthentication
{
    /**
     * This middleware bacause Auth::check() doesn't work if the endpoint is public
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only attempt authentication for API routes
        if ($request->is('api/*') && $request->bearerToken()) {
            try {
                // Attempt to authenticate with Sanctum
                $user = auth('sanctum')->user();

                if ($user) {
                    // Set the user in the default guard
                    Auth::setUser($user);
                }
            } catch (\Exception $e) {
                // Silently fail - this is optional auth
            }
        }

        return $next($request);
    }
}
