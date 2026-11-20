<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    /**
     * Menampilkan halaman daftar seluruh pengajuan lembur dari karyawan.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Query dasar dengan relasi lengkap (mendukung penamaan relasi bahasa Inggris & Indonesia)
        $query = Overtime::with([
            'user.position', 
            'user.branch', 
            'user.cabang', 
            'user.jabatan'
        ])->latest();

        // [FITUR UTAMA] Filter berdasarkan status jika ada parameter di URL
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // [FITUR TAMBAHAN] Pencarian berdasarkan nama atau NIK karyawan langsung dari controller
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $overtimes = $query->get();

        return view('admin.overtime.index', compact('overtimes'));
    }

    /**
     * [FITUR TAMBAHAN] Menampilkan detail pengajuan lembur tertentu (untuk kebutuhan Modal/Ajax).
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $overtime = Overtime::with([
                'user.position', 
                'user.branch', 
                'user.cabang', 
                'user.jabatan'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $overtime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data lembur tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Memproses persetujuan (approve) pengajuan lembur karyawan.
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        try {
            $overtime = Overtime::with('user')->findOrFail($id);
            
            // Ubah status pengajuan menjadi disetujui
            $overtime->update([
                'status' => 'approved'
            ]);

            return redirect()->back()->with('success', 'Pengajuan lembur atas nama ' . ($overtime->user->name ?? 'Karyawan') . ' berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses data: ' . $e->getMessage());
        }
    }

    /**
     * Memproses penolakan (reject) pengajuan lembur karyawan.
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        try {
            $overtime = Overtime::with('user')->findOrFail($id);
            
            // Ubah status pengajuan menjadi ditolak
            $overtime->update([
                'status' => 'rejected'
            ]);

            return redirect()->back()->with('success', 'Pengajuan lembur atas nama ' . ($overtime->user->name ?? 'Karyawan') . ' telah ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses data: ' . $e->getMessage());
        }
    }
}