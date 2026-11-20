<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom keluarga & kontak darurat yang belum ada
            if (!Schema::hasColumn('users', 'marital_status')) {
                $table->string('marital_status')->nullable()->after('last_education');
            }
            if (!Schema::hasColumn('users', 'children_count')) {
                $table->integer('children_count')->default(0)->after('marital_status');
            }
            if (!Schema::hasColumn('users', 'family_name')) {
                $table->string('family_name')->nullable()->after('children_count');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('family_name');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['marital_status', 'children_count', 'family_name', 'emergency_contact_name', 'emergency_contact_phone']);
        });
    }
};