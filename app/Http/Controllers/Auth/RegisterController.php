<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|unique:users,nik', // NIK harus unik
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed', // Harus ada field password_confirmation
        ], [
            'nik.unique' => 'NIK ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // 2. Buat User Baru
        User::create([
            'name' => $request->name,
            'nik' => $request->nik,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'employee', // Default jadi karyawan biasa
            'is_active' => true,  // Langsung aktif (atau false jika butuh approval admin)
        ]);

        // 3. Redirect ke Login
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}