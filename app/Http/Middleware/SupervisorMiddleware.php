<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SupervisorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isSupervisor()) {
            abort(403, 'Acesso restrito. Como supervisor, não tem permissão para aceder a esta área.');
        }

        return $next($request);
    }
}
