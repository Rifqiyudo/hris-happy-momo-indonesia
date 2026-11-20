<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Department; // PENTING: Import Model Department
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * 1. TAMPILKAN DAFTAR JABATAN
     */
    public function index(Request $request)
    {
        // A. Ambil Data Departemen (Untuk Dropdown di Modal Tambah/Edit)
        $departments = Department::orderBy('name', 'ASC')->get();

        // B. Query Jabatan dengan Eager Loading (biar hemat query database)
        $query = Position::with('department'); // Load relasi department

        // C. Logika Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('level', 'LIKE', "%{$search}%");
            });
        }

        // D. Ambil data dengan Pagination (10 per halaman)
        $positions = $query->latest()->paginate(10);

        // E. Return ke View
        return view('admin.master.position.index', compact('positions', 'departments'));
    }

    /**
     * 2. SIMPAN DATA BARU
     */
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'name'          => 'required|string|max:255|unique:positions,name',
            'department_id' => 'required|exists:departments,id', // Wajib ada di tabel departments
            'level'         => 'required|in:staff,supervisor,manager,director', // Wajib pilih salah satu
            'description'   => 'nullable|string',
        ], [
            'name.required'          => 'Nama jabatan wajib diisi.',
            'name.unique'            => 'Nama jabatan sudah terdaftar.',
            'department_id.required' => 'Silakan pilih departemen.',
            'level.required'         => 'Level jabatan wajib dipilih.',
        ]);

        // Simpan ke Database
        Position::create([
            'name'          => $request->name,
            'department_id' => $request->department_id,
            'level'         => $request->level,
            'description'   => $request->description,
            'base_salary'   => 0, // Default 0, bisa diupdate nanti di modul payroll
        ]);

        return redirect()->back()->with('success', 'Jabatan baru berhasil ditambahkan');
    }

    /**
     * 3. UPDATE DATA
     */
    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        // Validasi Update (Ignore ID sendiri saat cek unique name)
        $request->validate([
            'name'          => 'required|string|max:255|unique:positions,name,'.$id,
            'department_id' => 'required|exists:departments,id',
            'level'         => 'required|in:staff,supervisor,manager,director',
            'description'   => 'nullable|string',
        ]);

        // Update Database
        $position->update([
            'name'          => $request->name,
            'department_id' => $request->department_id,
            'level'         => $request->level,
            'description'   => $request->description,
        ]);

        return redirect()->back()->with('success', 'Data jabatan berhasil diperbarui');
    }

    /**
     * 4. HAPUS DATA
     */
    public function destroy($id)
    {
        $position = Position::findOrFail($id);

        // PROTEKSI: Cek apakah jabatan ini sedang dipakai oleh karyawan?
        // Kita gunakan relasi 'users' yang sudah dibuat di Model Position
        if ($position->users()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal hapus! Jabatan ini masih digunakan oleh karyawan aktif.');
        }
        
        $position->delete();
        
        return redirect()->back()->with('success', 'Jabatan berhasil dihapus');
    }
}