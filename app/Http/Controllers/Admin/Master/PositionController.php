<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::all();
        return view('admin.master.position.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.master.position.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'base_salary' => 'required|numeric',
        ]);

        Position::create($request->all());

        return redirect()->route('admin.master.position.index')->with('success', 'Jabatan ditambahkan');
    }

    public function edit($id)
    {
        $position = Position::findOrFail($id);
        return view('admin.master.position.edit', compact('position'));
    }

    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'base_salary' => 'required|numeric',
        ]);

        $position->update($request->all());

        return redirect()->route('admin.master.position.index')->with('success', 'Jabatan diperbarui');
    }

    public function destroy($id)
    {
        Position::destroy($id);
        return redirect()->back()->with('success', 'Jabatan dihapus');
    }
}