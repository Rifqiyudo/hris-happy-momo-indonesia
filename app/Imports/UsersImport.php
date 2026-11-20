<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * Memetakan setiap baris Excel menjadi Model User
     */
    public function model(array $row)
    {
        // Logika Email Otomatis jika kolom email di Excel kosong
        $email = $row['email'] ?? strtolower(Str::slug($row['name'], '')) . $row['nik'] . '@happymomo.id';

        return new User([
            'branch_id'     => $row['branch_id'],
            'department_id' => $row['department_id'],
            'position_id'   => $row['position_id'],
            'shift_id'      => $row['shift_id'],
            'name'          => strtoupper($row['name']),
            'nik'           => trim($row['nik']),
            'email'         => $email,
            'password'      => Hash::make($row['nik']), // Default password adalah NIK
            'role'          => $row['role'] ?? 'employee',
            'join_date'     => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['join_date']),
            'is_active'     => true,
            'barcode_token' => Str::random(32),
        ]);
    }

    /**
     * Aturan Validasi untuk setiap baris di Excel
     */
    public function rules(): array
    {
        return [
            'nik'       => 'required|unique:users,nik',
            'name'      => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'join_date' => 'required',
        ];
    }

    /**
     * Custom error messages (opsional)
     */
    public function customValidationMessages()
    {
        return [
            'nik.unique' => 'NIK :input sudah terdaftar di sistem.',
            'branch_id.exists' => 'ID Cabang :input tidak ditemukan.',
        ];
    }
}