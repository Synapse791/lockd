<?php

namespace Lockd\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;

class SetupCheck
{
    /**
     * Checks if setup has been completed
     *
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        if (!Storage::exists('setup.lock'))
            return redirect('/install');

        return $next($request);
    }
}
