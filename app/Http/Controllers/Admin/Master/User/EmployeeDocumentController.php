<?php

namespace App\Http\Controllers\Admin\Master\User;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeDocumentController extends Controller
{
    /**
     * Proses Upload dan Simpan Dokumen
     * Parameter $userId didapat dari rute: admin/documents/upload/{user}
     */
    public function upload(Request $request, $userId)
    {
        $request->validate([
            'type' => 'required|string|max:100',
            'file' => 'required|mimes:pdf,jpg,png,jpeg|max:5120', // Maksimal 5MB
        ], [
            'file.mimes' => 'Format file dokumen harus berupa PDF, JPG, atau PNG.',
            'file.max'   => 'Ukuran dokumen maksimal adalah 5MB.'
        ]);

        try {
            $user = User::findOrFail($userId);
            $file = $request->file('file');

            // Membersihkan input 'type' dari karakter ilegal untuk keamanan nama file
            $safeType = preg_replace('/[^A-Za-z0-9\-]/', '_', $request->type);

            // Nama file unik agar tidak menimpa file lama: KTP_IT001_17000000.pdf
            $fileName = strtoupper($safeType) . '_' . $user->nik . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke folder Private 'local' (storage/app/employee_docs) agar tidak bisa diakses publik via URL
            $path = $file->storeAs("employee_docs/{$user->nik}", $fileName, 'local');

            EmployeeDocument::create([
                'user_id'   => $user->id,
                'type'      => strtoupper($request->type),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                // Pastikan migration tabel dokumen Anda sudah memiliki kolom ini jika ingin menyimpannya
                'file_size' => round($file->getSize() / 1024), // Menyimpan ukuran dalam hitungan KB
            ]);

            return back()->with('success', 'Dokumen berhasil diarsip ke brankas sistem karyawan.');
            
        } catch (\Exception $e) {
            Log::error("Document Upload Error [User ID: {$userId}]: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan dokumen: Terjadi kesalahan pada server.');
        }
    }

    /**
     * Download Berkas Secara Aman
     * Menggunakan parameter tunggal $id sesuai rute: admin/documents/download/{id}
     */
    public function download($id)
    {
        try {
            $doc = EmployeeDocument::findOrFail($id);

            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();

            // VALIDASI KEAMANAN: Hanya Admin atau pemilik dokumen yang bisa akses
            if ($currentUser->role !== 'admin' && $currentUser->id !== $doc->user_id) {
                abort(403, 'Anda tidak memiliki izin untuk mengunduh berkas rahasia ini.');
            }

            // Cek apakah file fisik benar-benar ada di storage private
            if (!Storage::disk('local')->exists($doc->file_path)) {
                abort(404, 'Berkas fisik tidak ditemukan di server. Silakan hubungi Administrator IT.');
            }

            // Response download dari disk private (Memaksa browser melakukan unduhan)
            return Storage::disk('local')->download($doc->file_path, $doc->file_name);
            
        } catch (\Exception $e) {
            Log::error("Document Download Error [Doc ID: {$id}]: " . $e->getMessage());
            abort(500, 'Terjadi kesalahan sistem saat memproses unduhan dokumen.');
        }
    }

    /**
     * Hapus Dokumen Permanen
     * Menggunakan parameter tunggal $id sesuai rute: admin/documents/delete/{id}
     */
    public function destroy($id)
    {
        try {
            $doc = EmployeeDocument::findOrFail($id);
            
            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();

            // VALIDASI: Pastikan pengakses memiliki hak hapus
            if ($currentUser->role !== 'admin' && $currentUser->id !== $doc->user_id) {
                abort(403, 'Aksi ditolak. Anda tidak memiliki izin untuk menghapus berkas ini.');
            }

            // 1. Hapus file fisik secara permanen dari server
            if (Storage::disk('local')->exists($doc->file_path)) {
                Storage::disk('local')->delete($doc->file_path);
            }
            
            // 2. Hapus catatan rekam jejak dari database
            $doc->delete();

            return back()->with('success', 'Dokumen berhasil dihapus secara permanen dari server.');
            
        } catch (\Exception $e) {
            Log::error("Document Delete Error [Doc ID: {$id}]: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus dokumen: Terjadi kesalahan pada server.');
        }
    }
}