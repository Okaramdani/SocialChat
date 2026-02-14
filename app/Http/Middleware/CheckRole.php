<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Middleware untuk proteksi route berdasarkan role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();

        // Cek apakah user di-suspend
        if ($user->is_suspended) {
            Auth::logout();
            return redirect('/login')->with('error', 'Akun Anda di-suspend. Hubungi admin.');
        }

        // Cek role
        if ($role === 'admin' && !$user->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        if ($role === 'user' && $user->isAdmin()) {
            // Admin juga bisa akses route user
        }

        return $next($request);
    }
}
