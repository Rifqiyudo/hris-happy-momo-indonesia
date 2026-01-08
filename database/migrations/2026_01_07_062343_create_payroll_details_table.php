<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            // Terhubung ke tabel Payrolls di atas
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            
            $table->string('name'); // "Tunjangan Makan", "Potongan Telat", "Bonus Target"
            $table->decimal('amount', 15, 2);
            
            // Penentu apakah ini menambah atau mengurangi gaji
            $table->enum('type', ['allowance', 'deduction']); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
    }
};