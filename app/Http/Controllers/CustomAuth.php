<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login (ada session user)
        if (!$request->session()->has('user')) {
            return redirect()->route('login');
        }

        // Cek apakah user authenticated
        $user = $request->session()->get('user');
        if (!isset($user['authenticated']) || $user['authenticated'] !== true) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
