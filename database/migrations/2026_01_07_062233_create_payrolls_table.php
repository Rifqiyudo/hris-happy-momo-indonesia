<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Periode Gaji
            $table->string('month'); // "10" (Oktober)
            $table->string('year');  // "2025"
            $table->date('generated_date'); // Tanggal hitung gaji
            
            // Rekap Kehadiran (Disimpan agar transparan)
            $table->integer('total_days_worked')->default(0);
            $table->integer('total_late_minutes')->default(0);
            
            // Angka Final (Snapshot)
            // Kenapa snapshot? Agar jika bulan depan gaji pokok naik, history gaji bulan lalu tidak berubah
            $table->decimal('base_salary', 15, 2); 
            $table->decimal('total_allowance', 15, 2); // Total Tunjangan
            $table->decimal('total_deduction', 15, 2); // Total Potongan
            $table->decimal('net_salary', 15, 2); // Gaji Bersih (THP)
            
            $table->enum('status', ['draft', 'paid'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};