<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * INDEX: Menampilkan daftar pengajuan dengan statistik workflow.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $statusFilter = $request->get('status', 'Pending');

        // 1. QUERY UTAMA
        $query = LeaveRequest::with(['user.position', 'user.department']);

        // Filter Akses (PIC hanya lihat departemennya, Employee lihat miliknya sendiri)
        if ($user->role === 'pic') {
            $query->whereHas('user', fn($q) => $q->where('department_id', $user->department_id));
        } elseif ($user->role === 'employee') {
            $query->where('user_id', $user->id);
        }

        // --- LOGIKA FILTER TAB (PENTING AGAR DATA MUNCUL) ---
        if ($statusFilter === 'Approved') {
            // Gabungkan status dari PIC dan Admin agar muncul di tab "Disetujui"
            $query->whereIn('status', ['Approved_by_pic', 'Approved_by_admin']);
        } elseif ($statusFilter === 'Rejected') {
            $query->whereIn('status', ['Rejected_by_pic', 'Rejected_by_admin']);
        } elseif ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $leaves = $query->latest()->paginate(10);

        // 2. LOGIKA STATISTIK UNTUK CARDS
        $statsQuery = LeaveRequest::query();
        if ($user->role === 'pic') {
            $statsQuery->whereHas('user', fn($q) => $q->where('department_id', $user->department_id));
        } elseif ($user->role === 'employee') {
            $statsQuery->where('user_id', $user->id);
        }

        $stats = [
            'pending'  => (clone $statsQuery)->where('status', 'Pending')->count(),
            'approved' => (clone $statsQuery)->whereIn('status', ['Approved_by_pic', 'Approved_by_admin'])->count(),
            'rejected' => (clone $statsQuery)->whereIn('status', ['Rejected_by_pic', 'Rejected_by_admin'])->count(),
        ];

        return view('admin.transaksi.leave.index', compact('leaves', 'statusFilter', 'stats'));
    }

    /**
     * APPROVE: Method Router Utama
     * Menentukan apakah di-approve oleh PIC atau Admin berdasarkan role session.
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return $this->approveByAdmin($request, $id);
        } elseif ($user->role === 'pic') {
            return $this->approveByPic($request, $id);
        }
        
        abort(403, 'Akses ditolak.');
    }

    /**
     * APPROVE BY PIC: Langkah pertama (Supervisor).
     */
    protected function approveByPic(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        
        if ($leave->status !== 'Pending') {
            return back()->with('error', 'Status pengajuan sudah tidak dalam posisi Pending.');
        }

        $leave->update([
            'status'             => 'Approved_by_pic',
            'approved_by_pic_id' => Auth::id(),
            'approved_by_pic_at' => now(),
            'pic_note'           => $request->admin_note // Mengambil dari input SweetAlert
        ]);

        return redirect()->back()->with('success', 'Pengajuan disetujui oleh Supervisor. Menunggu verifikasi HRD.');
    }

    /**
     * APPROVE BY ADMIN: Langkah final (HRD).
     */
    protected function approveByAdmin(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $leave = LeaveRequest::with('user')->findOrFail($id);

            $leave->update([
                'status'               => 'Approved_by_admin',
                'approved_by_admin_id' => Auth::id(),
                'approved_by_admin_at' => now(),
                'admin_note'           => $request->admin_note
            ]);

            // Potong kuota jika tipe adalah Cuti
            if ($leave->type === 'Cuti') {
                $employee = $leave->user;
                if ($employee->leave_quota < $leave->total_days) {
                    throw new \Exception("Kuota cuti karyawan tidak mencukupi.");
                }
                $employee->decrement('leave_quota', $leave->total_days);
            }

            return redirect()->back()->with('success', 'Pengajuan disetujui final oleh HRD. Kuota cuti telah dipotong.');
        });
    }

    /**
     * REJECT: Bisa dilakukan PIC atau Admin.
     */
    public function reject(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $user  = Auth::user();

        $status = ($user->role === 'pic') ? 'Rejected_by_pic' : 'Rejected_by_admin';
        
        $leave->update([
            'status'     => $status,
            'admin_note' => $request->admin_note
        ]);

        return redirect()->back()->with('success', 'Pengajuan telah ditolak.');
    }

    /**
     * STORE: Pengajuan oleh karyawan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'       => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|max:500',
        ]);

        $data = $request->all();
        $data['user_id']    = Auth::id();
        $data['status']     = 'Pending';
        $data['total_days'] = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leave_attachments', 'public');
        }

        LeaveRequest::create($data);
        return redirect()->back()->with('success', 'Pengajuan berhasil dikirim.');
    }
}