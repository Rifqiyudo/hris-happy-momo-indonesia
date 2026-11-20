<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Kunci unik untuk memanggil pengaturan (misal: 'site_name', 'maintenance_mode')
            $table->string('key')->unique();
            
            // Nilai dari pengaturan tersebut. Menggunakan text agar bisa menampung data panjang atau format JSON
            $table->text('value')->nullable();
            
            // Tipe data untuk mempermudah casting di Model (misal: 'string', 'boolean', 'integer', 'json', 'image')
            $table->string('type')->default('string');
            
            // Pengelompokan pengaturan untuk mempermudah filter di UI/Admin Panel (misal: 'general', 'seo', 'payment')
            $table->string('group')->default('general')->index();
            
            // Deskripsi singkat kegunaan pengaturan ini
            $table->string('description')->nullable();
            
            // Status aktif/nonaktif pengaturan
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};