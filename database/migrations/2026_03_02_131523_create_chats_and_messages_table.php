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
        // 1. Tabel Chats: Wadah percakapan
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('hrd_id')->constrained('users')->onDelete('cascade');
            $table->string('subject')->nullable(); 
            $table->enum('status', ['open', 'closed'])->default('open');
            
            // Penanda aktivitas terakhir untuk sorting cepat di sidebar
            $table->timestamp('last_message_at')->nullable();
            
            $table->timestamps();
            
            // Indexing agar query pencarian chat per user sangat cepat
            $table->index(['user_id', 'hrd_id']);
        });

        // 2. Tabel Chat Messages: Isi pesan dan lampiran file
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            
            $table->text('message')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable(); // Misal: pdf, jpg, docx
            
            // Indikator pesan dibaca
            $table->boolean('is_read')->default(false);
            
            // Soft delete jika ingin menghapus pesan tanpa menghilangkan record
            $table->softDeletes();
            
            $table->timestamps();

            // Indexing untuk performa saat meload list pesan
            $table->index(['chat_id', 'created_at']);
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chats');
    }
};