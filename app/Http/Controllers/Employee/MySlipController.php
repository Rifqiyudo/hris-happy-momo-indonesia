<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll; // Pastikan Model Payroll sudah ada
use Illuminate\Support\Facades\Auth;
use PDF; // Jika pakai library barryvdh/laravel-dompdf

class MySlipController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil data gaji milik user ini, urutkan dari yang terbaru
        // Pastikan tabel 'payrolls' sudah ada (biasanya fitur Admin Penggajian)
        // Jika belum ada tabel payrolls, kode ini akan error di Model.
        // Asumsi: Kita sudah punya Model Payroll.
        
        $payrolls = Payroll::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('employee.slip.index', compact('payrolls'));
    }

    public function printPdf($id)
    {
        $payroll = Payroll::where('user_id', Auth::id())->findOrFail($id);
        
        // Load view khusus PDF (Kita buat sederhana saja dulu)
        // Jika belum install library PDF, kita return view biasa saja
        return view('employee.slip.pdf', compact('payroll'));
        
        // Jika pakai DOMPDF:
        // $pdf = PDF::loadView('employee.slip.pdf', compact('payroll'));
        // return $pdf->download('Slip-Gaji-'.$payroll->month.'.pdf');
    }
}