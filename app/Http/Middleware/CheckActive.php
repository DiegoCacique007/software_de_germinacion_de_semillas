<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) abort(403, 'No autenticado.');
        if (!$user->activo) abort(403, 'Tu cuenta se encuentra inactiva.');

        return $next($request);
    }
}
