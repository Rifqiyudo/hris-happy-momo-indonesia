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
        Schema::table('payrolls', function (Blueprint $table) {
            // Pengecekan kolom created_by agar tidak terjadi error duplicate
            if (!Schema::hasColumn('payrolls', 'created_by')) {
                $table->foreignId('created_by')
                      ->nullable()
                      ->after('status')
                      ->constrained('users')
                      ->onDelete('set null');
            }

            // Pengecekan kolom payment_date agar tidak terjadi error duplicate
            if (!Schema::hasColumn('payrolls', 'payment_date')) {
                $table->timestamp('payment_date')
                      ->nullable()
                      ->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Menghapus foreign key dan kolom jika rollback dilakukan
            if (Schema::hasColumn('payrolls', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }

            if (Schema::hasColumn('payrolls', 'payment_date')) {
                $table->dropColumn('payment_date');
            }
        });
    }
};