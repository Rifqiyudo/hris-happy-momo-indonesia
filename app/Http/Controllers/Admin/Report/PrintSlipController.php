<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan library sudah terinstall

class PrintSlipController extends Controller
{
    public function printPdf($id)
    {
        // Ambil data gaji beserta detailnya
        $payroll = Payroll::with(['user.position', 'details'])->findOrFail($id);
        
        // Load View PDF (Kita buat nanti view-nya)
        $pdf = Pdf::loadView('admin.report.pdf.slip_gaji', compact('payroll'));
        
        // Set ukuran kertas struk atau A4 (disini pakai A4 dibagi 2 atau A5)
        $pdf->setPaper('a5', 'landscape');

        // Download / Stream
        return $pdf->stream('Slip_Gaji_' . $payroll->user->name . '_' . $payroll->month . '.pdf');
    }
}