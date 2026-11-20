<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * 1. INDEX: Menampilkan daftar cabang & jumlah karyawan.
     * Mendukung pencarian berdasarkan kode, nama, atau alamat.
     */
    public function index(Request $request)
    {
        $query = Branch::withCount('users');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        $branches = $query->latest()->paginate(10)->withQueryString();
        
        return view('admin.master.branch.index', compact('branches'));
    }

    /**
     * 2. CREATE: Form tambah cabang baru.
     */
    public function create()
    {
        return view('admin.master.branch.create');
    }

    /**
     * 3. STORE: Menyimpan data cabang baru hasil input peta/GPS.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'      => 'required|string|max:20|unique:branches,code',
            'name'      => 'required|string|max:255',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'required|integer|min:10',
            'address'   => 'nullable|string',
            'phone'     => 'nullable|string|max:20',
        ], [
            'code.unique'       => 'Kode cabang sudah digunakan.',
            'latitude.required' => 'Titik lokasi di peta wajib ditandai.',
            'latitude.numeric'  => 'Format koordinat latitude tidak valid.',
            'longitude.numeric' => 'Format koordinat longitude tidak valid.',
            'radius.min'        => 'Radius minimal absensi adalah 10 meter.',
        ]);

        try {
            DB::beginTransaction();
            
            Branch::create($validated);

            DB::commit();
            return redirect()->route('admin.master.branch.index')
                             ->with('success', 'Cabang Baru & Radius Absensi berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal Store Cabang: " . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    /**
     * 4. SHOW: Melihat detail peta dan simulasi radius cabang.
     */
    public function show($id)
    {
        $branch = Branch::withCount('users')->findOrFail($id);
        return view('admin.master.branch.show', compact('branch'));
    }

    /**
     * 5. EDIT: Form edit untuk merubah koordinat atau radius.
     */
    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('admin.master.branch.edit', compact('branch'));
    }

    /**
     * 6. UPDATE: Memperbarui data lokasi/radius cabang.
     * Mengabaikan ID sendiri pada validasi unique 'code'.
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            // Rule::unique memastikan kode tidak bentrok dengan data sendiri saat di-update
            'code'      => ['required', 'string', 'max:20', Rule::unique('branches')->ignore($branch->id)],
            'name'      => 'required|string|max:255',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'required|integer|min:10',
            'address'   => 'nullable|string',
            'phone'     => 'nullable|string|max:20',
        ], [
            'latitude.required' => 'Mohon tentukan kembali titik koordinat di peta.',
            'radius.min'        => 'Radius minimal absensi adalah 10 meter.',
        ]);

        try {
            DB::beginTransaction();

            $branch->update($validated);

            DB::commit();
            
            // Jika request datang dari halaman index (modal/inline edit)
            return redirect()->route('admin.master.branch.index')
                             ->with('success', "Data cabang {$branch->name} berhasil diperbarui.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal Update Cabang ID {$id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui data lokasi cabang.');
        }
    }

    /**
     * 7. DESTROY: Menghapus cabang dengan proteksi integritas data karyawan.
     */
    public function destroy($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            
            // Proteksi: Mencegah penghapusan jika masih ada karyawan aktif yang terikat ke cabang ini
            if ($branch->users()->exists()) {
                $count = $branch->users()->count();
                return back()->with('error', "Gagal: Cabang ini masih memiliki {$count} karyawan aktif. Pindahkan karyawan sebelum menghapus cabang.");
            }

            $branch->delete();
            return back()->with('success', 'Data cabang berhasil dihapus dari sistem.');

        } catch (\Exception $e) {
            Log::error("Gagal Delete Cabang: " . $e->getMessage());
            return back()->with('error', 'Data tidak bisa dihapus karena terkait dengan riwayat absensi atau data sistem lainnya.');
        }
    }
}