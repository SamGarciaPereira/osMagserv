<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class OrcamentistaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isOrcamentista()) {
            abort(403, 'Acesso restrito. Como orçamentista, não tem permissão para aceder a esta área.');
        }

        return $next($request);
    }
}