<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::user();

        $roles = ['doctor', 'admin'];

        if (!$user || !in_array($user->role, $roles)) {
            return redirect()->route('user-home');
            // abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
