<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Relasi ke User (Jika user dihapus, data absen hilang)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->date('date'); // Tanggal Absen
            
            // Jam
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            
            // Lokasi & Foto (Bukti)
            $table->string('location_lat')->nullable();
            $table->string('location_long')->nullable();
            $table->string('photo_in')->nullable(); // Foto Selfie Masuk
            $table->string('photo_out')->nullable(); // Foto Selfie Pulang
            
            // Analisa Sistem (Otomatis diisi logic backend)
            $table->integer('late_minutes')->default(0); // Telat berapa menit?
            $table->boolean('is_late')->default(false); 
            $table->string('status')->default('alpha'); // present, sick, permit, alpha
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};