<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Library PDF yang sama dengan Admin

class MySlipController extends Controller
{
    /**
     * Menampilkan daftar riwayat gaji karyawan.
     */
    public function index()
    {
        $userId = Auth::id();

        // Ambil data payroll milik user yang sedang login
        // Syarat: Status harus 'paid' (sudah dipublish admin)
        $payrolls = Payroll::where('user_id', $userId)
                        ->where('status', 'paid') 
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->get();

        return view('employee.slip.index', compact('payrolls'));
    }

    /**
     * Cetak / Download Slip Gaji ke PDF
     */
    public function printPdf($id)
    {
        $userId = Auth::id();

        // Validasi Keamanan:
        // Pastikan ID payroll yang diminta benar-benar milik user ini.
        // Jangan sampai user A bisa download slip user B dengan ganti ID di URL.
        $payroll = Payroll::with(['user.position', 'details'])
                        ->where('user_id', $userId)
                        ->where('id', $id)
                        ->where('status', 'paid')
                        ->firstOrFail(); // Jika tidak ketemu (bukan miliknya), akan error 404

        // Kita gunakan view PDF yang SAMA dengan milik Admin agar desainnya konsisten
        // Lokasi view: resources/views/admin/report/pdf/slip_gaji.blade.php
        $pdf = Pdf::loadView('admin.report.pdf.slip_gaji', compact('payroll'));
        
        // Set ukuran kertas (A5 Landscape agar mirip struk)
        $pdf->setPaper('a5', 'landscape');

        // Stream: Membuka file di browser (bisa didownload manual)
        return $pdf->stream('Slip_Gaji_' . Auth::user()->name . '_' . $payroll->month . '-' . $payroll->year . '.pdf');
    }
}