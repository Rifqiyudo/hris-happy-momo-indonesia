<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
// use Maatwebsite\Excel\Facades\Excel; // Nanti jika pakai library Excel

class ExportController extends Controller
{
    public function exportAttendance()
    {
        // SEMENTARA: Redirect dulu karena kita belum install library Excel
        return redirect()->back()->with('info', 'Fitur Export Excel akan segera hadir (Library belum diinstall).');
    }
}