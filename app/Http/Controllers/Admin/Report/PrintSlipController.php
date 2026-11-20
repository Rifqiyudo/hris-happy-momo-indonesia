<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PrintSlipController extends Controller
{
    /**
     * Cetak Slip Gaji ke PDF (Sesuai Konsep Gaji Harian & BPJS Paten)
     */
    public function printPdf($id)
    {
        // 1. Ambil data payroll beserta relasi user, posisi, dan departemen
        $payroll = Payroll::with(['user.position', 'user.department'])->findOrFail($id);
        $user = $payroll->user;

        // 2. Hitung ulang hari hadir riil untuk ditampilkan di deskripsi slip (Poin 12)
        // Ini memastikan angka "22 Hari" atau "20 Hari" muncul di PDF
        $daysPresent = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->whereIn('status', ['hadir', 'telat', 'present'])
            ->count();

        // 3. Siapkan data untuk dikirim ke view PDF
        $data = [
            'payroll'     => $payroll,
            'user'        => $user,
            'daysPresent' => $daysPresent,
            'dailySalary' => $user->position->daily_salary ?? 0,
            'allowance'   => $user->position->allowance ?? 0,
            'periode'     => Carbon::createFromDate($payroll->year, $payroll->month, 1)->translatedFormat('F Y'),
        ];

        // 4. Load View dan setting ukuran kertas A5 Landscape (Khas Slip Gaji)
        // FIXED: Memperluas pengecekan berkas (Fallback) agar mencakup semua nama file yang berpotensi digunakan
        if (view()->exists('admin.report.slip-gaji-pdf')) {
            $viewName = 'admin.report.slip-gaji-pdf';
        } elseif (view()->exists('admin.report.pdf.slip_gaji')) {
            $viewName = 'admin.report.pdf.slip_gaji';
        } else {
            $viewName = 'admin.report.slip_gaji';
        }

        $pdf = Pdf::loadView($viewName, $data);
        $pdf->setPaper('a5', 'landscape');

        // 5. Stream ke Browser
        return $pdf->stream('Slip_Gaji_' . str_replace(' ', '_', $user->name) . '_' . $payroll->month . '.pdf');
    }

    /**
     * TAMBAHAN TERBARU: Alias Method untuk menjamin keselarasan Route resource jika memanggil nama method berbeda
     */
    public function print($id)
    {
        return $this->printPdf($id);
    }
}