<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

// --- TAMBAHKAN BARIS INI JIKA BELUM ADA ---
use App\Exports\AttendanceExport; 
// ------------------------------------------

class ExportDataController extends Controller
{
    public function exportAttendance(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Script ini memanggil Class AttendanceExport yang baru kita buat
        return Excel::download(new AttendanceExport($month, $year), 'Laporan_Absensi_'.$month.'-'.$year.'.xlsx');
    }
}