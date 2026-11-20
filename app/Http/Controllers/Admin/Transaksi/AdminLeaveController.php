<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\LeaveApprovedNotification; 
// use App\Notifications\LeaveRejectedNotification; // Aktifkan jika sudah buat classnya

class AdminLeaveController extends Controller
{
    /**
     * 1. INDEX: Monitoring Pengajuan Cuti & Izin
     * Mengambil data dengan Eager Loading agar Foto & Detail Cabang muncul.
     */
    public function index(Request $request)
    {
        // A. Filter Status (default: pending)
        $status = $request->get('status', 'pending'); 

        /** * B. Query Utama
         * 'user' memuat attribute avatar_url dari model User.
         * Pastikan relasi di model Leave adalah 'user'.
         */
        $query = Leave::with([
            'user' => function($q) {
                $q->select('id', 'name', 'avatar', 'nik', 'branch_id', 'position_id', 'department_id');
            }, 
            'user.position', 
            'user.department', 
            'user.branch'
        ]);

        // C. Terapkan Filter Status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // D. Pagination
        $leaves = $query->orderBy('created_at', 'DESC')->paginate(10);
        $leaves->appends(['status' => $status]);

        // E. Statistik Widget (Optimasi: Satu query untuk semua count)
        $statsRaw = Leave::select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')
                        ->get()
                        ->pluck('total', 'status');

        $stats = [
            'pending'  => $statsRaw['pending'] ?? 0,
            'approved' => $statsRaw['approved'] ?? 0,
            'rejected' => $statsRaw['rejected'] ?? 0,
            'total'    => $statsRaw->sum(),
        ];

        return view('admin.transaksi.leave.index', compact('leaves', 'stats', 'status'));
    }

    /**
     * 2. APPROVE: Setujui Pengajuan
     */
    public function approve($id)
    {
        try {
            DB::beginTransaction();
            
            $leave = Leave::with('user')->findOrFail($id);
            
            if ($leave->status !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
            }

            // A. Update Status
            $leave->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // B. Kirim Notifikasi
            // Jika foto profil tidak muncul di dashboard karyawan, pastikan model User menggunakan trait Notifiable
            if ($leave->user) {
                $leave->user->notify(new LeaveApprovedNotification($leave));
            }

            DB::commit();
            return redirect()->back()->with('success', "Permohonan {$leave->user->name} berhasil DISETUJUI.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Approve Leave: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    /**
     * 3. REJECT: Tolak Pengajuan
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $leave = Leave::with('user')->findOrFail($id);

            if ($leave->status !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
            }

            $leave->update([
                'status'      => 'rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'admin_note'  => $request->reason,
            ]);

            DB::commit();
            return redirect()->back()->with('success', "Permohonan {$leave->user->name} telah DITOLAK.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Reject Leave: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak pengajuan.');
        }
    }
}