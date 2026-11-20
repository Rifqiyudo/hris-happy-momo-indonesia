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
        Schema::table('leave_requests', function (Blueprint $table) {
            // Menggunakan pengecekan hasColumn agar tidak terjadi error jika kolom sudah ada
            if (!Schema::hasColumn('leave_requests', 'total_days')) {
                $table->integer('total_days')->default(0)->after('end_date');
            }
            
            if (!Schema::hasColumn('leave_requests', 'is_unpaid')) {
                $table->boolean('is_unpaid')->default(false)->after('total_days');
            }
            
            if (!Schema::hasColumn('leave_requests', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Drop kolom hanya jika kolom tersebut benar-benar ada
            $columnsToDrop = [];
            if (Schema::hasColumn('leave_requests', 'total_days')) $columnsToDrop[] = 'total_days';
            if (Schema::hasColumn('leave_requests', 'is_unpaid')) $columnsToDrop[] = 'is_unpaid';
            if (Schema::hasColumn('leave_requests', 'admin_note')) $columnsToDrop[] = 'admin_note';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};