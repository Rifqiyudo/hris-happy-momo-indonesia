<?php

namespace App\Http\Controllers\Admin\Master\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        // Menampilkan semua user (Kecuali admin jika perlu difilter)
        $users = User::with('position')->where('role', 'employee')->latest()->get();
        return view('admin.master.user.index', compact('users'));
    }

    public function create()
    {
        $positions = Position::all();
        return view('admin.master.user.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nik' => 'required|unique:users',
            'position_id' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee', // Default role
            'nik' => $request->nik,
            'phone' => $request->phone,
            'position_id' => $request->position_id,
            'address' => $request->address,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'is_active' => true,
        ]);

        return redirect()->route('admin.master.user.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $positions = Position::all();
        return view('admin.master.user.edit', compact('user', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'nik' => 'required|unique:users,nik,'.$id,
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'phone' => $request->phone,
            'position_id' => $request->position_id,
            'address' => $request->address,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'is_active' => $request->has('is_active'),
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.master.user.index')->with('success', 'Data karyawan diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->avatar) {
            Storage::delete('public/' . $user->avatar);
        }
        $user->delete();
        return redirect()->back()->with('success', 'Karyawan dihapus');
    }
}