<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        // PERBAIKAN: Mengambil data user yang sedang login beserta relasinya (Eager Loading).
        // Hal ini wajib dilakukan agar data branch (Unit Kerja), department, dan position bisa dibaca di View.
        $user = User::with(['branch', 'department', 'position'])->findOrFail(Auth::id());
        
        // Memanggil view resources/views/admin/profile.blade.php dengan membawa data user yang sudah lengkap
        return view('admin.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|max:2048', // Maksimal 2MB
            'password' => 'nullable|min:6|confirmed' // Pastikan input password confirm ada name="password_confirmation"
        ]);

        // 1. UPDATE DATA DASAR
        $user->name = $request->name;
        $user->email = $request->email;

        // 2. LOGIKA UPLOAD FOTO (PENTING)
        if ($request->hasFile('avatar')) {
            // Hapus foto lama jika ada & bukan null
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Simpan foto baru ke folder 'avatars' di storage public
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // 3. UPDATE PASSWORD (JIKA DIISI)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}