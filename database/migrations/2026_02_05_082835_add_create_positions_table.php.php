<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Proteksi: Cek jika tabel belum ada
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                
                // Relasi ke Departemen (Pastikan tabel departments sudah dibuat sebelumnya)
                $table->foreignId('department_id')->constrained()->onDelete('cascade');
                
                $table->string('level')->default('staff'); // director, manager, pic, staff
                
                // Logika Penggajian (Sesuai error base_salary sebelumnya)
                $table->decimal('base_salary', 15, 2)->default(0); 
                $table->decimal('daily_salary', 15, 2)->default(0);
                $table->decimal('allowance', 15, 2)->default(0);
                $table->decimal('overtime_rate', 15, 2)->default(0);
                
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Matikan constraint dulu agar proses drop tidak error
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('positions');
        Schema::enableForeignKeyConstraints();
    }
};