<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; 

class MyHistoryController extends Controller
{
    /**
     * 1. INDEX: List Riwayat Absensi
     * Menampilkan daftar absen dengan pagination dan filter waktu dinamis.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Menangkap filter bulan & tahun (default ke bulan/tahun ini)
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        // Query dengan Eager Loading untuk efisiensi memory
        $history = Attendance::where('user_id', $user->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->orderBy('date', 'desc')
                    ->paginate(15) // Sesuaikan jumlah item per halaman untuk mobile
                    ->withQueryString();

        // Data pendukung untuk filter di View
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Daftar tahun (3 tahun terakhir)
        $years = range(Carbon::now()->year, Carbon::now()->year - 2);

        return view('employee.history.index', compact('history', 'month', 'year', 'months', 'years'));
    }

    /**
     * 2. SHOW: Detail Riwayat Absensi
     * Menampilkan bukti foto, koordinat peta, dan status keterlambatan.
     */
    public function show($id)
    {
        // Security Check: Hanya izinkan akses jika data milik user yang sedang login
        $attendance = Attendance::where('user_id', Auth::id())
                                ->with(['user.position', 'user.department'])
                                ->findOrFail($id);

        return view('employee.history.show', compact('attendance'));
    }

    /**
     * 3. DOWNLOAD PDF: Bukti Presensi Harian
     * Menghasilkan slip bukti absen resmi untuk keperluan klaim atau arsip.
     */
    public function downloadPdf($id)
    {
        try {
            // Security Check & Data Retrieval
            $attendance = Attendance::where('user_id', Auth::id())
                                    ->with(['user.position', 'user.department', 'user.branch'])
                                    ->findOrFail($id);

            // Render view khusus PDF (Pastikan file resources/views/employee/history/pdf.blade.php tersedia)
            $pdf = Pdf::loadView('employee.history.pdf', compact('attendance'))
                      ->setPaper('a4', 'portrait')
                      ->setOptions([
                          'isHtml5ParserEnabled' => true,
                          'isRemoteEnabled' => true // Penting untuk memuat gambar/foto dari storage
                      ]);

            $fileName = 'PRESENSI_' . $attendance->date->format('Y-m-d') . '_' . Auth::user()->nik . '.pdf';
            
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh PDF: ' . $e->getMessage());
        }
    }
}