<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        // Urutkan berdasarkan hari (Senin=1 s/d Minggu=7)
        $shifts = Shift::orderBy('day_number')->get();
        return view('admin.master.shift.index', compact('shifts'));
    }

    public function edit($id)
    {
        $shift = Shift::findOrFail($id);
        return view('admin.master.shift.edit', compact('shift'));
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);
        
        $request->validate([
            'clock_in' => 'required',
            'clock_out' => 'required',
        ]);

        $shift->update([
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            // 'is_holiday' bisa ditambahkan logic checkbox jika ingin mengubah hari libur
        ]);

        return redirect()->route('admin.master.shift.index')->with('success', 'Jam kerja berhasil diupdate');
    }
}