<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $depts = ['Human Resource', 'IT Support', 'Gudang', 'Operasional', 'Marketing'];
        foreach ($depts as $dept) {
            Department::firstOrCreate(['name' => $dept]);
        }
    }
}