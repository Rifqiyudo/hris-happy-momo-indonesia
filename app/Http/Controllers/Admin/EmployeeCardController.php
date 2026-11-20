<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Picqer\Barcode\BarcodeGeneratorPNG;

class EmployeeCardController extends Controller
{
    public function print($id)
    {
        $user = User::with('position')->findOrFail($id);
        $generator = new BarcodeGeneratorPNG();
        
        // Generate barcode berdasarkan NIK
        $barcode = base64_encode($generator->getBarcode($user->nik, $generator::TYPE_CODE_128));

        return view('admin.employee.print-card', compact('user', 'barcode'));
    }
}