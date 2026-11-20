<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf; // Import DomPDF untuk generate file PDF
use Carbon\Carbon;

class MyPayrollController extends Controller
{
    /**
     * 1. DAFTAR RIWAYAT GAJI
     * Menampilkan list slip gaji yang sudah diterbitkan.
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil data gaji milik user ini, urutkan dari yang terbaru
        // HANYA tampilkan yang statusnya 'paid' (sudah terbit/dibayar)
        $payrolls = Payroll::where('user_id', $user->id)
                           ->where('status', 'paid') 
                           ->orderByDesc('year')
                           ->orderByDesc('month')
                           ->paginate(12); // Menggunakan pagination agar rapi jika data banyak

        return view('employee.payroll.index', compact('payrolls'));
    }

    /**
     * 2. DETAIL SLIP GAJI (Tampilan Web/HP)
     * Digunakan untuk preview di layar smartphone sebelum diunduh.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Cari data payroll, pastikan milik user yang login (Security Check)
        $payroll = Payroll::where('user_id', $user->id)
                          ->where('id', $id)
                          ->where('status', 'paid')
                          ->firstOrFail();

        return view('employee.payroll.show', compact('payroll', 'user'));
    }

    /**
     * 3. CETAK / DOWNLOAD PDF
     * Fungsi utama untuk menghasilkan file PDF yang bisa disimpan di HP.
     */
    public function printPdf($id)
    {
        $user = Auth::user();

        // Security Check: Pastikan data milik user sendiri & status paid
        $payroll = Payroll::where('user_id', $user->id)
                          ->where('id', $id)
                          ->where('status', 'paid')
                          ->firstOrFail();

        // Data yang dikirim ke view PDF
        $data = [
            'payroll' => $payroll,
            'user'    => $user,
            'company' => 'PT. Happy Momo Indonesia',
            'date'    => Carbon::now()->translatedFormat('d F Y H:i')
        ];

        // Load view khusus PDF, set ukuran kertas A4 potrait
        $pdf = Pdf::loadView('employee.payroll.pdf', $data)
                  ->setPaper('a4', 'portrait');

        // Format nama file: Slip_Gaji_NIK_Bulan_Tahun.pdf
        $fileName = 'Slip_Gaji_' . $user->nik . '_' . $payroll->month . '_' . $payroll->year . '.pdf';

        // Langsung download ke perangkat karyawan
        return $pdf->download($fileName);
    }
}