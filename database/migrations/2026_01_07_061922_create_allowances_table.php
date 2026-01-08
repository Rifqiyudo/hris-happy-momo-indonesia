<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Uang Makan", "Tunjangan Jabatan"
            $table->decimal('amount', 15, 2); // Nominal default
            
            // Logic Penting:
            // 'daily' = Cair jika hadir (dikali jumlah kehadiran)
            // 'monthly' = Cair utuh per bulan (flat)
            $table->enum('type', ['daily', 'monthly'])->default('monthly'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allowances');
    }
};