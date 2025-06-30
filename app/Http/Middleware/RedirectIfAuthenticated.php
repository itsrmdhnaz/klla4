<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.master-data.pegawai.index', absolute: false));
        } elseif ($user->hasRole('user')) {
            return redirect()->intended(route('user.index', absolute: false));
        }

        return $next($request); // Allow guests to proceed
    }
}
