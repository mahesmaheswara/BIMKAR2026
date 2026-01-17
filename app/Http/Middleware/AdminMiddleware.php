<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {//jelasin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // If not admin, log out the user
        Auth:: logout();
        return redirect('/login');
    }
}