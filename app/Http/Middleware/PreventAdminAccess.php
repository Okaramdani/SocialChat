<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak dapat mengakses fitur user. Silakan gunakan panel admin.');
        }

        return $next($request);
    }
}
