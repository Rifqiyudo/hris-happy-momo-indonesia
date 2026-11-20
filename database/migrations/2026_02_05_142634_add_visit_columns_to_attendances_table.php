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
        Schema::table('attendances', function (Blueprint $table) {
            // Gunakan if !Schema::hasColumn untuk mencegah error saat migrate
            if (!Schema::hasColumn('attendances', 'category')) {
                $table->string('category')->nullable()->after('status');
            }
            if (!Schema::hasColumn('attendances', 'latitude')) {
                $table->string('latitude')->nullable()->after('category');
            }
            if (!Schema::hasColumn('attendances', 'longitude')) {
                $table->string('longitude')->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('attendances', 'address')) {
                $table->text('address')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('attendances', 'image_visit')) {
                $table->string('image_visit')->nullable()->after('address');
            }
            if (!Schema::hasColumn('attendances', 'late_minutes')) {
                $table->integer('late_minutes')->default(0)->after('image_visit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan pengecekan sebelum drop agar tidak error 'Can't DROP column'
            $columns = ['category', 'latitude', 'longitude', 'address', 'image_visit', 'late_minutes'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('attendances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};