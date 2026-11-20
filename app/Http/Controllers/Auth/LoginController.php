<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses autentikasi login.
     */
    public function login(Request $request)
    {
        // 1. Validasi Input Kredensial
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.'
        ]);

        // Menangkap fitur "Remember Me" (Ingat Saya) jika ada di form
        $remember = $request->boolean('remember'); 

        // 2. Cek Database (Attempt Login)
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // =========================================================
            // FITUR KEAMANAN: Blokir Akun Non-Aktif / Resign
            // =========================================================
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Catat ke log server backend sebagai peringatan keamanan
                Log::warning("Percobaan login ditolak: Akun non-aktif mencoba mengakses sistem.", ['email' => $credentials['email']]);

                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan atau berstatus resign. Silakan hubungi Administrator HRD.',
                ])->onlyInput('email');
            }

            // Mencegah serangan Session Fixation (Wajib untuk standar keamanan ISO)
            $request->session()->regenerate();

            // =========================================================
            // FITUR AUDIT LOG: Catat Aktivitas Login
            // =========================================================
            if (function_exists('activity')) {
                activity('auth_system')
                    ->causedBy($user)
                    ->withProperties(['ip' => $request->ip(), 'agent' => $request->userAgent()])
                    ->log('Berhasil login ke dalam sistem HRIS.');
            }

            // =========================================================
            // LOGIKA REDIRECT BERDASARKAN ROLE (HAK AKSES)
            // =========================================================
            $role = $user->role;

            // Admin dan PIC -> Diarahkan ke Admin Dashboard
            if ($role === 'admin' || $role === 'pic') {
                return redirect()->intended(route('admin.dashboard'))
                                 ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
            } 
            // Karyawan -> Diarahkan ke Employee Dashboard (ESS)
            elseif ($role === 'employee') {
                return redirect()->intended(route('employee.dashboard'))
                                 ->with('success', 'Selamat bekerja, ' . $user->name . '!');
            } 
            // FALLBACK KEAMANAN: Jika Role tidak valid/kosong, paksa keluar!
            else {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                Log::error("Akses ditolak: Tingkat otorisasi tidak valid.", ['email' => $user->email, 'role' => $role]);
                
                return redirect()->route('login')->with('error', 'Akses ditolak. Tingkat otorisasi akun Anda tidak terdaftar di sistem.');
            }
        }

        // 3. Jika Gagal (Kredensial tidak cocok di database)
        // Delay respons sejenak untuk mencegah serangan Brute-Force
        usleep(500000); // 0.5 detik

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses logout pengguna.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Catat aktivitas logout ke dalam sistem Audit sebelum sesi dihapus
        if ($user && function_exists('activity')) {
            activity('auth_system')
                ->causedBy($user)
                ->withProperties(['ip' => $request->ip()])
                ->log('Telah keluar (logout) dari sistem HRIS.');
        }

        // Keluarkan pengguna dari sesi
        Auth::logout();
        
        // Hapus dan reset token keamanan sesi secara menyeluruh
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem dengan aman.');
    }
}