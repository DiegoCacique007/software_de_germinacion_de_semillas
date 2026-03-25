<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle(Request $request, Closure $next, string $role)
{
    if (auth()->user()->role !== $role) {
        return redirect('dashboard')->with('error', 'No tienes permisos de ' . $role);
    }

    return $next($request);
}
}
