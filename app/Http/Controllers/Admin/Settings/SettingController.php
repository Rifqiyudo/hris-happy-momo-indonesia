<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

// Import Model Spatie untuk Role dan Audit Log
use Spatie\Permission\Models\Role;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse; // Ditambahkan untuk fitur Export CSV

class SettingController extends Controller
{
    /**
     * ========================================================================
     * 1. MANAJEMEN ROLE & AKSES
     * ========================================================================
     * Menampilkan daftar peran (role) dan hak akses pengguna.
     */
    public function role()
    {
        // Mengambil data role dinamis dari database (Spatie Permission)
        // dan menghitung jumlah user di masing-masing role untuk analitik UI
        $roles = Role::with('permissions')->withCount('users')->get();

        return view('admin.settings.role', compact('roles'));
    }

    /**
     * ========================================================================
     * 2. KONFIGURASI PERUSAHAAN (View)
     * ========================================================================
     * Menampilkan form pengaturan identitas dan profil perusahaan.
     */
    public function company()
    {
        // Mengambil semua pengaturan perusahaan dari tabel settings secara dinamis
        // Diubah menjadi key-value pair array agar mudah dipanggil di Blade
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        return view('admin.settings.company', compact('settings'));
    }

    /**
     * ========================================================================
     * 3. MEMPROSES UPDATE KONFIGURASI PERUSAHAAN (Action)
     * ========================================================================
     */
    public function updateCompany(Request $request)
    {
        // Validasi ketat untuk mencegah injeksi data berbahaya
        $request->validate([
            'company_name'    => 'required|string|max:255',
            'company_email'   => 'required|email|max:255',
            'company_phone'   => 'nullable|string|max:50',
            'company_website' => 'nullable|url|max:255',
            'company_npwp'    => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'currency'        => 'required|string|max:10',
            'timezone'        => 'required|string|max:50',
            'work_start_time' => 'required',
            'work_end_time'   => 'required',
            'company_logo'    => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // 1. Ambil semua input kecuali token dan file gambar
            $inputs = $request->except(['_token', '_method', 'company_logo']);

            // 2. Simpan atau Update data teks ke tabel settings (Batch Processing)
            foreach ($inputs as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $value, 'updated_at' => now()]
                );
            }

            // 3. Handle Upload Logo (Hanya diproses jika ada file baru yang diunggah)
            if ($request->hasFile('company_logo')) {
                $file = $request->file('company_logo');
                
                // Cari eksistensi logo lama di database
                $oldLogo = DB::table('settings')->where('key', 'company_logo')->first();
                
                // Hapus logo lama dari server fisik agar storage tidak membengkak (Garbage Collection)
                if ($oldLogo && $oldLogo->value && Storage::disk('public')->exists($oldLogo->value)) {
                    Storage::disk('public')->delete($oldLogo->value);
                }

                // Format penamaan file logo baru yang unik
                $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Simpan ke direktori storage/app/public/logos
                $path = $file->storeAs('logos', $fileName, 'public');

                // Simpan path gambar ke database
                DB::table('settings')->updateOrInsert(
                    ['key' => 'company_logo'],
                    ['value' => $path, 'updated_at' => now()]
                );
            }

            // 4. Hapus Cache Global agar perubahan langsung berdampak di seluruh sistem
            Cache::forget('app_settings');

            // 5. Catat aktivitas ini ke Log sistem secara otomatis
            activity('system_config')
                ->causedBy(auth()->user())
                ->log('Administrator memperbarui konfigurasi identitas dan operasional perusahaan.');

            DB::commit();
            return redirect()->route('admin.settings.company')->with('success', 'Profil dan konfigurasi perusahaan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui profil perusahaan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * ========================================================================
     * 4. AUDIT LOG (Riwayat Aktivitas & Keamanan)
     * ========================================================================
     * Menampilkan riwayat aktivitas sistem dengan fitur Filter Pencarian Lanjutan.
     */
    public function audit(Request $request)
    {
        // Memulai query builder dengan memanggil relasi 'causer' (Pelaku Aktivitas)
        $query = Activity::with('causer')->latest();

        // --- FITUR FILTERING ---
        
        // 1. Filter Berdasarkan Tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 2. Filter Berdasarkan Nama Pengguna (Pencarian Relasional)
        if ($request->filled('user')) {
            $query->whereHas('causer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        // 3. Filter Berdasarkan Tipe Aktivitas (Create, Update, Delete, Login)
        if ($request->filled('action')) {
            $action = strtolower($request->action);
            $query->where(function($q) use ($action) {
                $q->where('event', 'like', '%' . $action . '%')
                  ->orWhere('description', 'like', '%' . $action . '%');
            });
        }

        // Mengeksekusi query dan melakukan paginasi (20 data per halaman)
        // withQueryString() memastikan URL filter tidak hilang saat pindah halaman paginasi
        $logs = $query->paginate(20)->withQueryString();
        
        return view('admin.settings.audit', compact('logs'));
    }

    /**
     * ========================================================================
     * 5. EXPORT AUDIT LOG (FITUR BARU)
     * ========================================================================
     * Mengekspor riwayat log aktivitas ke format CSV (Excel) berdasarkan filter.
     */
    public function exportAudit(Request $request)
    {
        // Gunakan logika query yang sama dengan method audit() agar data export sama dengan data yang difilter
        $query = Activity::with('causer')->latest();

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('user')) {
            $query->whereHas('causer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }
        if ($request->filled('action')) {
            $action = strtolower($request->action);
            $query->where(function($q) use ($action) {
                $q->where('event', 'like', '%' . $action . '%')
                  ->orWhere('description', 'like', '%' . $action . '%');
            });
        }

        // Ambil semua data yang sudah terfilter (tanpa pagination)
        $logs = $query->get();

        $fileName = 'Audit_Log_HRIS_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Format data ke dalam Stream CSV
        $callback = function() use($logs) {
            $file = fopen('php://output', 'w');
            
            // Tulis Header Kolom
            fputcsv($file, ['Tanggal', 'Waktu', 'Pengguna', 'Role', 'Tipe Aktivitas', 'Modul', 'Deskripsi', 'Detail Payload']);

            // Looping baris data
            foreach ($logs as $log) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($log->created_at)->format('Y-m-d'),
                    \Carbon\Carbon::parse($log->created_at)->format('H:i:s'),
                    $log->causer->name ?? 'System Automated / Deleted User',
                    $log->causer->role ?? 'Sistem',
                    strtoupper($log->event),
                    strtoupper(str_replace('_', ' ', $log->log_name)),
                    $log->description,
                    $log->properties ? json_encode($log->properties) : '-'
                ]);
            }
            fclose($file);
        };

        // Catat aktivitas mengekspor ini ke dalam log sistem itu sendiri
        activity('system_audit')
            ->causedBy(auth()->user())
            ->log('Mengekspor data Audit Log sistem ke format CSV.');

        return new StreamedResponse($callback, 200, $headers);
    }
}