<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\Position; // Tambahan: Import Model Position
use App\Models\User;     // Tambahan: Import Model User untuk target spesifik
use Illuminate\Http\Request;

class AllowanceController extends Controller
{
    /**
     * 1. INDEX: Menampilkan Daftar Tunjangan
     * Mengurutkan berdasarkan nama agar rapi di tabel.
     */
    public function index()
    {
        // Tambahan: Eager load 'position' dan 'user' agar relasi terbaca di tabel
        $allowances = Allowance::with(['position', 'user'])->orderBy('name', 'ASC')->get();
        
        // Tambahan: Ambil daftar jabatan untuk ditampilkan di dropdown form
        $positions = Position::orderBy('name', 'ASC')->get();

        // Tambahan: Ambil daftar karyawan aktif untuk ditampilkan di dropdown form
        $users = User::where('is_active', true)->orderBy('name', 'ASC')->get();

        // Tambahan: Compact variabel $positions dan $users agar bisa dibaca di file Blade
        // PERBAIKAN: Mengubah path view dari 'tunjangan.index' menjadi 'allowance.index' agar sesuai nama folder
        return view('admin.master.allowance.index', compact('allowances', 'positions', 'users'));
    }

    /**
     * 2. STORE: Menyimpan Tunjangan Baru
     */
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'name'        => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'type'        => 'required|in:fixed,daily', // Sesuai enum database baru
            'target_id'   => 'nullable|string', // Tambahan: Validasi target_id gabungan (pos_ atau usr_)
        ], [
            'name.required'      => 'Nama tunjangan wajib diisi.',
            'amount.required'    => 'Nominal wajib diisi.',
            'amount.numeric'     => 'Nominal harus berupa angka.',
            'type.required'      => 'Tipe tunjangan wajib dipilih.',
        ]);

        // ====================================================================
        // LOGIKA PEMISAHAN TARGET (JABATAN ATAU KARYAWAN SPESIFIK)
        // ====================================================================
        $position_id = null;
        $user_id = null;

        if ($request->target_id) {
            // Jika valuenya berawalan 'pos_', berarti targetnya Jabatan
            if (str_starts_with($request->target_id, 'pos_')) {
                $position_id = str_replace('pos_', '', $request->target_id);
            } 
            // Jika valuenya berawalan 'usr_', berarti targetnya Karyawan spesifik
            elseif (str_starts_with($request->target_id, 'usr_')) {
                $user_id = str_replace('usr_', '', $request->target_id);
            }
        }

        // Simpan ke Database
        Allowance::create([
            'name'        => $request->name,
            'amount'      => $request->amount,
            'type'        => $request->type,
            'position_id' => $position_id, // Disimpan dari hasil logika di atas
            'user_id'     => $user_id,     // Disimpan dari hasil logika di atas
            // Checkbox HTML: Jika dicentang kirim '1', jika tidak kirim null/tidak ada.
            // Gunakan $request->has(...) untuk mengubahnya jadi boolean true/false.
            'is_taxable'  => $request->has('is_taxable'), 
            'is_active'   => true, // Default aktif saat dibuat
            'description' => $request->description, // Opsional
        ]);

        return redirect()->back()->with('success', 'Komponen tunjangan berhasil ditambahkan.');
    }

    /**
     * 3. UPDATE: Memperbarui Data Tunjangan
     */
    public function update(Request $request, $id)
    {
        $allowance = Allowance::findOrFail($id);
        
        // Validasi
        $request->validate([
            'name'        => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'type'        => 'required|in:fixed,daily',
            'target_id'   => 'nullable|string', // Tambahan: Validasi target_id saat update
        ]);

        // ====================================================================
        // LOGIKA PEMISAHAN TARGET (JABATAN ATAU KARYAWAN SPESIFIK)
        // ====================================================================
        $position_id = null;
        $user_id = null;

        if ($request->target_id) {
            if (str_starts_with($request->target_id, 'pos_')) {
                $position_id = str_replace('pos_', '', $request->target_id);
            } elseif (str_starts_with($request->target_id, 'usr_')) {
                $user_id = str_replace('usr_', '', $request->target_id);
            }
        }

        // Update Data
        $allowance->update([
            'name'        => $request->name,
            'amount'      => $request->amount,
            'type'        => $request->type,
            'position_id' => $position_id, // Update ID Jabatan jika ada
            'user_id'     => $user_id,     // Update ID Karyawan jika ada
            // Logic checkbox sama seperti store
            'is_taxable'  => $request->has('is_taxable'),
            // Jika Anda nanti menambah checkbox 'is_active' di form edit, tambahkan disini:
            // 'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Data tunjangan berhasil diperbarui.');
    }

    /**
     * 4. DESTROY: Menghapus Tunjangan
     */
    public function destroy($id)
    {
        // Menggunakan findOrFail agar jika ID salah, muncul 404 (bukan error system)
        $allowance = Allowance::findOrFail($id);
        
        // Soft Delete (Sesuai model & migrasi yang sudah pakai SoftDeletes)
        $allowance->delete(); 
        
        return redirect()->back()->with('success', 'Komponen tunjangan berhasil dihapus.');
    }
}