<?php

namespace Lockd\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {

            if ($request->hasHeader('Authorization') && str_contains($request->header('Authorization'), 'Bearer')) {
                // TODO Authenticate using OAuth2 when implemented
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return new JsonResponse([
                        'data' => [],
                        'error' => 'unauthorized',
                        'errorDescription' => 'You need to be properly authenticated to access that',
                    ], 401);
                } else {
                    return redirect()->guest('/');
                }
            }
        }

        return $next($request);
    }
}
