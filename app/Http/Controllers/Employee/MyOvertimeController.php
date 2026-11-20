<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Overtime; 

class MyOvertimeController extends Controller
{
    /**
     * 1. LIST RIWAYAT: Menampilkan riwayat lembur milik karyawan sendiri
     */
    public function index()
    {
        $overtimes = Overtime::where('user_id', Auth::id())
                             ->latest()
                             ->paginate(10); // Menggunakan paginate agar lebih rapi jika data banyak

        return view('employee.overtime.index', compact('overtimes'));
    }

    /**
     * 2. FORM PENGAJUAN: Menampilkan form input lembur
     */
    public function create()
    {
        return view('employee.overtime.create');
    }

    /**
     * 3. PROSES SIMPAN: Validasi, Hitung Durasi Menit, dan Simpan
     */
    public function store(Request $request)
    {
        // A. Validasi Input
        $request->validate([
            'date'       => 'required|date',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
            'reason'     => 'required|string|max:255',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ], [
            'end_time.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
            'attachment.max' => 'Ukuran foto bukti maksimal 2MB.',
        ]);

        // B. Hitung Durasi (PENTING: Diubah ke Menit agar sesuai Database)
        $start = Carbon::parse($request->start_time);
        $end   = Carbon::parse($request->end_time);
        
        // Menggunakan diffInMinutes untuk mendapatkan integer murni
        $durationMinutes = $start->diffInMinutes($end);

        // C. Upload Lampiran (Jika ada bukti pengerjaan)
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            // Penamaan file yang rapi: idUser_timestamp.ext
            $filename = Auth::id() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('overtime_proofs', $filename, 'public');
        }

        // D. Simpan ke Database (Memperbaiki QueryException: Field duration_minutes)
        Overtime::create([
            'user_id'          => Auth::id(),
            'date'             => $request->date,
            'start_time'       => $request->start_time,
            'end_time'         => $request->end_time,
            'duration_minutes' => $durationMinutes, // Pastikan nama kolom sesuai DB
            'reason'           => $request->reason,
            'attachment'       => $attachmentPath,
            'status'           => 'pending', 
        ]);

        return redirect()->route('employee.overtime.index')
                         ->with('success', 'Pengajuan lembur berhasil dikirim. Menunggu persetujuan Admin.');
    }

    /**
     * 4. DETAIL: Melihat detail status dan bukti lembur
     */
    public function show($id)
    {
        // Security Check: Hanya bisa melihat milik sendiri
        $overtime = Overtime::where('user_id', Auth::id())
                            ->findOrFail($id);

        return view('employee.overtime.show', compact('overtime'));
    }

    /**
     * 5. CANCEL: Batalkan pengajuan (Hanya jika status masih pending)
     */
    public function destroy($id)
    {
        $overtime = Overtime::where('user_id', Auth::id())
                            ->where('status', 'pending')
                            ->findOrFail($id);
        
        // Hapus file bukti jika ada
        if ($overtime->attachment) {
            Storage::disk('public')->delete($overtime->attachment);
        }

        $overtime->delete();

        return redirect()->back()->with('success', 'Pengajuan lembur berhasil dibatalkan.');
    }
}