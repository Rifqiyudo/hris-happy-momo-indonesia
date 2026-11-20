<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * 1. MENAMPILKAN DATA (INDEX)
     * Fitur: Pagination, Search, & Hitung Jumlah Pegawai
     */
    public function index(Request $request)
    {
        // Query Dasar dengan menghitung relasi users (karyawan)
        // withCount('users') akan membuat properti virtual 'users_count'
        $query = Department::withCount('users');

        // Logika Pencarian (Search Bar)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Urutkan berdasarkan nama A-Z dan paginasi 10 data per halaman
        $departments = $query->orderBy('name', 'ASC')->paginate(10);

        // Agar parameter pencarian tetap ada saat pindah halaman (page 1 ke 2)
        $departments->appends($request->all());

        return view('admin.master.department.index', compact('departments'));
    }

    /**
     * 2. SIMPAN DATA BARU (STORE)
     */
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            // Nama wajib, string, maks 255 huruf, dan harus unik di tabel departments
            'name'        => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama departemen wajib diisi.',
            'name.unique'   => 'Nama departemen ini sudah ada, silakan gunakan nama lain.',
        ]);

        // Simpan ke Database
        Department::create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    /**
     * 3. UPDATE DATA (UPDATE)
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        // Validasi Input
        $request->validate([
            // Unique mengecualikan ID diri sendiri (. $id) agar tidak error saat update tanpa ganti nama
            'name'        => 'required|string|max:255|unique:departments,name,'.$id,
            'description' => 'nullable|string|max:500',
        ]);

        // Update Database
        $department->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Data departemen berhasil diperbarui.');
    }

    /**
     * 4. HAPUS DATA (DESTROY)
     * Fitur: Proteksi agar tidak menghapus departemen yang ada karyawannya.
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        // Cek apakah ada karyawan di departemen ini?
        // Kita gunakan users() relasi yang ada di Model Department
        if ($department->users()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal hapus! Masih ada karyawan yang terdaftar di departemen ini. Silakan pindahkan karyawan tersebut terlebih dahulu.');
        }

        // Cek apakah ada Jabatan (Position) di departemen ini? (Opsional, jika ada relasi jabatan)
        if ($department->positions()->count() > 0) {
             return redirect()->back()->with('error', 'Gagal hapus! Masih ada Jabatan yang terhubung dengan departemen ini.');
        }

        $department->delete();

        return redirect()->back()->with('success', 'Departemen berhasil dihapus.');
    }
}