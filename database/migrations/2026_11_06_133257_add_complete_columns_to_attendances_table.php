<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kolom untuk fitur deteksi telat otomatis
            if (!Schema::hasColumn('attendances', 'is_late')) {
                $table->boolean('is_late')->default(false)->after('status');
                $table->integer('late_minutes')->default(0)->after('is_late');
            }

            // Kolom untuk fitur kategori (Office/Visit)
            if (!Schema::hasColumn('attendances', 'category')) {
                $table->string('category', 50)->default('office')->after('clock_out');
            }

            // Kolom untuk fitur GPS & Alamat Visit
            if (!Schema::hasColumn('attendances', 'latitude')) {
                $table->string('latitude')->nullable()->after('category');
                $table->string('longitude')->nullable()->after('latitude');
                $table->text('address')->nullable()->after('longitude');
            }

            // Kolom untuk fitur Kiosk Foto (Webcam)
            if (!Schema::hasColumn('attendances', 'image_in')) {
                $table->string('image_in')->nullable()->after('address');
                $table->string('image_out')->nullable()->after('image_in');
                $table->string('image_visit')->nullable()->after('image_out');
            }
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'is_late', 'late_minutes', 'category', 
                'latitude', 'longitude', 'address', 
                'image_in', 'image_out', 'image_visit'
            ]);
        });
    }
};