<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\{Asset, AssetAssignment, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Auth, Schema, Storage};

class AssetController extends Controller
{
    /**
     * Menampilkan daftar aset dengan relasi user (Dilengkapi Pencarian & Filter).
     */
    public function index(Request $request)
    {
        $query = Asset::with(['assignments' => function($q) {
            $q->whereNull('return_date')->with('user');
        }]);

        // 1. Logika Fitur Pencarian (Search Bar)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
                
                // Fallback pencarian untuk nama kolom lama jika masih ada
                if (Schema::hasColumn('assets', 'asset_name')) {
                    $q->orWhere('asset_name', 'like', "%{$search}%");
                }
                if (Schema::hasColumn('assets', 'asset_serial_number')) {
                    $q->orWhere('asset_serial_number', 'like', "%{$search}%");
                }
            });
        }

        // 2. Logika Filter Dropdown (Status)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Appends agar query filter tidak hilang saat pindah halaman paginasi
        $assets = $query->latest()->paginate(15)->withQueryString();
        
        $users = User::where('is_active', true)->get();
        return view('admin.transaksi.asset.index', compact('assets', 'users'));
    }

    /**
     * Menampilkan form tambah aset baru.
     */
    public function create()
    {
        $employees = User::with('branch')
                         ->where('is_active', true)
                         ->orderBy('name', 'asc')
                         ->get();

        return view('admin.transaksi.asset.create', compact('employees'));
    }

    /**
     * Menyimpan aset baru ke database (Support Multi-Insert Spesifik Item).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Berbentuk Array Spesifik
        $request->validate([
            'user_id'          => 'nullable|exists:users,id',
            'name'             => 'required|array|min:1',
            'name.*'           => 'required|string|max:255',
            'brand'            => 'nullable|array',
            'serial_number'    => 'required|array|min:1',
            'serial_number.*'  => 'required|string|max:255',
            'category'         => 'required|array',
            'purchase_price'   => 'required|array',
            'purchase_date'    => 'nullable|array',
            'location'         => 'nullable|array',
            'condition'        => 'required|array',
            'notes'            => 'nullable|array',
            'image'            => 'nullable|array',
            'image.*'          => 'nullable|image|mimes:jpeg,png,jpg,svg|max:102400', // Validasi 100MB per gambar
        ], [
            'name.*.required'          => 'Semua baris Nama Aset wajib diisi.',
            'serial_number.*.required' => 'Semua baris Serial Number wajib diisi.',
            'image.*.max'              => 'Ukuran gambar tidak boleh melebihi 100MB.'
        ]);

        try {
            DB::beginTransaction();

            $userId = $request->input('user_id');
            $statusAset = $userId ? 'assigned' : 'available';
            $assignedDate = $userId ? now() : null;

            $insertedCount = 0;

            // 2. Looping Sebanyak Baris "Nama Aset" yang Ditambahkan HRD
            foreach ($request->name as $index => $name) {
                
                if (empty($name)) continue; // Pengamanan baris kosong

                // 3. Proses Upload Gambar Individual per Indeks
                $imagePath = null;
                if ($request->hasFile("image.$index") && $request->file("image.$index")->isValid()) {
                    $imagePath = $request->file("image.$index")->store('assets/uploads', 'public');
                }

                // Persiapan Data
                $assetData = [
                    'name'           => $name,
                    // Mendukung penamaan array brand[] atau asset_brand[] dari frontend
                    'brand'          => $request->brand[$index] ?? $request->asset_brand[$index] ?? null, 
                    'serial_number'  => $request->serial_number[$index],
                    'category'       => $request->category[$index] ?? 'electronics',
                    'condition'      => $request->condition[$index] ?? 'good',
                    'location'       => $request->location[$index] ?? null,
                    'purchase_price' => $request->purchase_price[$index] ?? 0,
                    'purchase_date'  => $request->purchase_date[$index] ?? null,
                    // Mendukung name form notes[] atau description[]
                    'notes'          => $request->notes[$index] ?? $request->description[$index] ?? null, 
                    'image'          => $imagePath,
                    'status'         => $statusAset
                ];

                // Memasukkan field user_id langsung ke tabel assets jika kolomnya ada
                if (Schema::hasColumn('assets', 'user_id')) {
                    $assetData['user_id'] = $userId;
                }
                if (Schema::hasColumn('assets', 'assigned_date')) {
                    $assetData['assigned_date'] = $assignedDate;
                }

                // 4. Simpan Data Aset ke Database
                $asset = Asset::create($assetData);

                // 5. Jika HRD memilih Karyawan, buat riwayat peminjaman (Assignment) otomatis
                if ($userId) {
                    AssetAssignment::create([
                        'user_id'         => $userId,
                        'asset_id'        => $asset->id,
                        'assigned_date'   => $assignedDate,
                        'condition_notes' => 'Alokasi awal multi-aset saat pendaftaran.'
                    ]);
                }

                $insertedCount++;
            }

            DB::commit();
            return redirect()->route('admin.transaksi.asset.index')
                             ->with('success', "Berhasil mendaftarkan {$insertedCount} perangkat ke dalam sistem.");
                             
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Asset Multi-Store Error: " . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memproses pendaftaran aset: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail dan riwayat peminjaman aset.
     */
    public function show($id)
    {
        $asset = Asset::with(['assignments.user'])->findOrFail($id);
        return view('admin.transaksi.asset.show', compact('asset'));
    }

    /**
     * Proses Memberikan Aset ke Karyawan (Assign) secara manual dari halaman detail
     */
    public function assign(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'asset_id'      => 'required|exists:assets,id',
            'assigned_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $asset = Asset::findOrFail($request->asset_id);
            if ($asset->status !== 'available') {
                return back()->with('error', 'Aset tidak tersedia untuk dipinjam saat ini.');
            }

            AssetAssignment::create([
                'user_id'         => $request->user_id,
                'asset_id'        => $request->asset_id,
                'assigned_date'   => $request->assigned_date,
                'condition_notes' => $request->condition_notes
            ]);

            // Update user_id di tabel utama jika kolomnya ada
            $updateData = ['status' => 'assigned'];
            if (Schema::hasColumn('assets', 'user_id')) {
                $updateData['user_id'] = $request->user_id;
            }

            $asset->update($updateData);

            DB::commit();
            Log::info("Aset {$asset->name} dialokasikan ke User ID: {$request->user_id} oleh Admin: " . Auth::id());
            
            return back()->with('success', 'Aset berhasil diserahkan ke karyawan.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Asset Assign Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Proses Pengembalian Aset ke Gudang
     */
    public function returnAsset($id)
    {
        try {
            DB::beginTransaction();

            $assignment = AssetAssignment::where('id', $id)->whereNull('return_date')->firstOrFail();
            
            $assignment->update(['return_date' => now()]);
            
            $updateData = ['status' => 'available'];
            if (Schema::hasColumn('assets', 'user_id')) {
                $updateData['user_id'] = null;
            }

            Asset::where('id', $assignment->asset_id)->update($updateData);

            DB::commit();
            return back()->with('success', 'Aset telah dikembalikan dan status diperbarui menjadi Tersedia.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Asset Return Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memproses pengembalian aset.');
        }
    }

    /**
     * =============================================================
     * FITUR EDIT, UPDATE, & DESTROY
     * =============================================================
     */

    /**
     * Menampilkan form edit aset.
     */
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $employees = User::where('is_active', true)->orderBy('name', 'asc')->get();
        
        return view('admin.transaksi.asset.edit', compact('asset', 'employees'));
    }

    /**
     * Memperbarui data aset di database.
     */
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:255',
            'serial_number'  => 'required|string|unique:assets,serial_number,' . $id,
            'category'       => 'required',
            'purchase_price' => 'required|numeric',
            'status'         => 'required|in:available,assigned,broken,maintenance',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,svg|max:102400',
        ]);

        try {
            $data = $request->except(['_token', '_method']);

            // Memetakan input catatan
            if ($request->has('notes')) {
                $data['notes'] = $request->notes;
            }

            if ($request->hasFile('image')) {
                // Hapus foto lama dari storage jika ada
                if ($asset->image) {
                    Storage::disk('public')->delete($asset->image);
                }
                $data['image'] = $request->file('image')->store('assets/uploads', 'public');
            }

            $asset->update($data);

            return redirect()->route('admin.transaksi.asset.index')->with('success', 'Informasi aset berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus aset secara permanen/soft delete.
     */
    public function destroy($id)
    {
        try {
            $asset = Asset::findOrFail($id);
            
            // Pengamanan: Cegah penghapusan jika aset sedang dibawa karyawan
            if ($asset->status === 'assigned') {
                return back()->with('error', 'Tidak dapat menghapus aset yang sedang dalam pemakaian karyawan. Tarik aset terlebih dahulu.');
            }

            // Hapus gambar fisik dari storage jika ada
            if ($asset->image) {
                Storage::disk('public')->delete($asset->image);
            }

            // Hapus relasi riwayat peminjaman (Assignment) terlebih dahulu agar tidak ada yatim piatu data
            AssetAssignment::where('asset_id', $id)->delete();

            $asset->delete();
            return redirect()->route('admin.transaksi.asset.index')->with('success', 'Aset berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus aset: ' . $e->getMessage());
        }
    }
}