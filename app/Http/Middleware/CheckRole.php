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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles  // Menerima banyak parameter role
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Ambil role user yang sedang login
        $userRole = Auth::user()->role;

        // 3. Cek apakah role user ada dalam daftar role yang diizinkan di Route
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 4. Jika tidak memiliki akses, lempar ke dashboard masing-masing dengan pesan error
        $redirectTo = ($userRole === 'admin') ? 'admin.dashboard' : 'employee.dashboard';
        
        return redirect()->route($redirectTo)->with('error', 'Anda tidak memiliki hak akses untuk halaman tersebut.');
    }
}