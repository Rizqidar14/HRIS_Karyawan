<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }

        $user = $request->session()->get('user');

        if (!isset($user['authenticated']) || $user['authenticated'] !== true) {
            return redirect()->route('login')->with('error', 'Sesi telah berakhir, silakan login kembali!');
        }

        return $next($request);
    }
}
