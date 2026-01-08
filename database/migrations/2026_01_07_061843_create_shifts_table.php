<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Regular Weekday", "Saturday Short"
            $table->integer('day_number'); // 1=Senin, 2=Selasa ... 6=Sabtu, 7=Minggu
            $table->time('clock_in');      // 08:00:00
            $table->time('clock_out');     // 17:00:00 atau 16:00:00
            $table->boolean('is_holiday')->default(false); // Penanda hari libur
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};