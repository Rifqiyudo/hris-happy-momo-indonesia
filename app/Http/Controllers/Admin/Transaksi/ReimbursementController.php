<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReimbursementController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan Klaim & Kasbon.
     * Dilengkapi dengan fitur pencarian dan filter status.
     */
    public function index(Request $request)
    {
        // Inisiasi query dengan Eager Loading relasi yang dibutuhkan
        $query = Reimbursement::with(['user' => function($q) {
            $q->select('id', 'name', 'nik', 'position_id', 'branch_id')
              ->with(['position:id,name', 'branch:id,name']);
        }]);

        // Filter berdasarkan Status (Pending, Approved, Rejected)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fitur Pencarian berdasarkan nama karyawan, NIK, atau deskripsi klaim
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        // Mengambil data dengan paginasi dan mempertahankan parameter URL
        $reimbursements = $query->latest('created_at')->paginate(15)->withQueryString();

        // Mengambil statistik ringkas untuk ditampilkan di UI (Opsional)
        $stats = [
            'pending'  => Reimbursement::where('status', 'Pending')->count(),
            'approved' => Reimbursement::where('status', 'Approved')->whereMonth('created_at', now()->month)->count(),
            'rejected' => Reimbursement::where('status', 'Rejected')->whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.transaksi.reimbursement.index', compact('reimbursements', 'stats'));
    }

    /**
     * Menampilkan detail spesifik dari satu pengajuan.
     */
    public function show($id)
    {
        $reimbursement = Reimbursement::with(['user.position', 'user.branch'])->findOrFail($id);
        return view('admin.transaksi.reimbursement.show', compact('reimbursement'));
    }

    /**
     * Menyetujui pengajuan Klaim / Kasbon (Approve).
     */
    public function approve($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $reimbursement = Reimbursement::findOrFail($id);
                
                // [PERBAIKAN] Menggunakan strtolower agar aman dari perbedaan huruf kapital di database
                if (strtolower($reimbursement->status) !== 'pending') {
                    throw new \Exception('Status klaim/kasbon ini sudah diproses sebelumnya.');
                }

                // [UPDATE] Mengaktifkan pencatatan pelaksana persetujuan (Admin ID & Timestamp)
                $updateData = [
                    'status' => 'Approved',
                ];

                // Cek aman jika kolom approved_by / approved_at ada pada model/tabel
                if (\Schema::hasColumn('reimbursements', 'approved_by')) {
                    $updateData['approved_by'] = Auth::id();
                }
                if (\Schema::hasColumn('reimbursements', 'approved_at')) {
                    $updateData['approved_at'] = now();
                }

                $reimbursement->update($updateData);

                // Mencatat Log Audit untuk keamanan finansial perusahaan
                Log::info("Klaim/Kasbon ID {$id} senilai Rp {$reimbursement->amount} disetujui oleh Admin ID " . Auth::id());
            });

            return back()->with('success', 'Pengajuan Klaim / Kasbon berhasil disetujui dan dicatat dalam sistem finansial.');

        } catch (\Exception $e) {
            Log::error("Gagal menyetujui klaim ID {$id}: " . $e->getMessage());
            return back()->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    /**
     * Menolak pengajuan Klaim / Kasbon (Reject) beserta alasannya.
     */
    public function reject(Request $request, $id)
    {
        // Validasi input alasan penolakan
        $request->validate([
            'reason' => 'required|string|max:500'
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi untuk memberitahu karyawan.',
            'reason.max'      => 'Alasan penolakan maksimal 500 karakter.'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $reimbursement = Reimbursement::findOrFail($id);
                
                // [PERBAIKAN] Menggunakan strtolower agar aman dari perbedaan huruf kapital di database
                if (strtolower($reimbursement->status) !== 'pending') {
                    throw new \Exception('Status klaim/kasbon ini sudah diproses sebelumnya.');
                }

                $updateData = [
                    'status'           => 'Rejected',
                    'rejection_reason' => $request->reason,
                ];

                if (\Schema::hasColumn('reimbursements', 'approved_by')) {
                    $updateData['approved_by'] = Auth::id();
                }
                if (\Schema::hasColumn('reimbursements', 'approved_at')) {
                    $updateData['approved_at'] = now();
                }

                $reimbursement->update($updateData);
                
                // Mencatat Log Audit untuk keamanan
                Log::info("Klaim/Kasbon ID {$id} ditolak oleh Admin ID " . Auth::id() . " dengan alasan: " . $request->reason);
            });

            return back()->with('success', 'Pengajuan Klaim / Kasbon berhasil ditolak.');

        } catch (\Exception $e) {
            Log::error("Gagal menolak klaim ID {$id}: " . $e->getMessage());
            return back()->with('error', 'Gagal memproses penolakan: ' . $e->getMessage());
        }
    }

    /**
     * [FITUR BARU] Metode tambahan untuk menangani perubahan status fleksibel via tombol PUT
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Pending,approved,rejected,pending',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $reimbursement = Reimbursement::findOrFail($id);
                
                // Normalisasi string huruf kapital pertama agar konsisten dengan database
                $newStatus = ucfirst(strtolower($request->status));

                $reimbursement->update([
                    'status' => $newStatus,
                ]);

                Log::info("Status Klaim/Kasbon ID {$id} diubah menjadi {$newStatus} oleh Admin ID " . Auth::id());
            });

            return back()->with('success', 'Status pengajuan klaim/kasbon berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Gagal mengubah status klaim ID {$id}: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}