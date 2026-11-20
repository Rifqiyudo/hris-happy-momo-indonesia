<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MyLeaveController extends Controller
{
    /**
     * 1. LIST PENGAJUAN (INDEX)
     * Ditambahkan logika filter status agar sinkron dengan workflow Admin (PIC/HRD)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $statusFilter = $request->get('status', 'Pending');

        $query = LeaveRequest::where('user_id', $user->id);

        // --- FIX LOGIKA FILTER STATUS (TERBARU) ---
        // Memastikan status bertingkat terbaca dengan benar di sisi karyawan
        if ($statusFilter === 'Approved') {
            $query->whereIn('status', ['Approved_by_pic', 'Approved_by_admin']);
        } elseif ($statusFilter === 'Rejected') {
            $query->whereIn('status', ['Rejected_by_pic', 'Rejected_by_admin']);
        } elseif ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $leaves = $query->latest()->paginate(10);

        // Menghitung statistik sederhana untuk tab di view karyawan
        $stats = [
            'pending'  => LeaveRequest::where('user_id', $user->id)->where('status', 'Pending')->count(),
            'approved' => LeaveRequest::where('user_id', $user->id)->whereIn('status', ['Approved_by_pic', 'Approved_by_admin'])->count(),
        ];

        return view('employee.leave.index', compact('leaves', 'statusFilter', 'stats'));
    }

    /**
     * 2. FORM PENGAJUAN (CREATE)
     */
    public function create()
    {
        return view('employee.leave.create');
    }

    /**
     * 3. PROSES SIMPAN (STORE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'       => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $durasi = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        
        // Logika Unpaid: Jika saldo cuti user (leave_quota) kurang dari durasi
        $saldoCuti = $user->leave_quota ?? 0;
        $isUnpaid = ($request->type === 'Cuti') ? ($saldoCuti < $durasi) : false;

        DB::beginTransaction();

        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = 'leave_' . Auth::id() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $attachmentPath = $file->storeAs('leave_attachments', $filename, 'public');
            }

            LeaveRequest::create([
                'user_id'    => Auth::id(),
                'type'       => $request->type,
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
                'reason'     => $request->reason,
                'attachment' => $attachmentPath,
                'total_days' => $durasi,
                'is_unpaid'  => $isUnpaid,
                'status'     => 'Pending' // Default awal selalu Huruf Kapital agar konsisten
            ]);

            DB::commit();

            $msg = $isUnpaid ? 'Pengajuan terkirim. Saldo cuti tidak mencukupi, akan masuk kategori Unpaid.' : 'Pengajuan berhasil dikirim.';
            return redirect()->route('employee.leave.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($attachmentPath)) Storage::disk('public')->delete($attachmentPath);
            Log::error("Gagal simpan izin: " . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function show($id)
    {
        $leave = LeaveRequest::where('user_id', Auth::id())->findOrFail($id);
        return view('employee.leave.show', compact('leave'));
    }

    public function destroy($id)
    {
        $leave = LeaveRequest::where('user_id', Auth::id())
                      ->where('status', 'Pending')
                      ->findOrFail($id);

        if ($leave->attachment) Storage::disk('public')->delete($leave->attachment);
        $leave->delete();

        return redirect()->route('employee.leave.index')->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}