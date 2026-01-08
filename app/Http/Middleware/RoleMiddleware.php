<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah role user sesuai dengan yang diminta rute
        if (Auth::user()->role !== $role) {
            // Jika Admin coba akses halaman Karyawan -> Lempar ke Admin Dashboard
            if(Auth::user()->role == 'admin') {
                 return redirect()->route('admin.dashboard');
            }
            // Jika Karyawan coba akses halaman Admin -> Lempar ke Portal Karyawan
            return redirect()->route('employee.dashboard');
        }

        return $next($request);
    }
}