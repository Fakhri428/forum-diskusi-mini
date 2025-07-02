<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Hapus foreign key thread_id
            $table->dropForeign(['thread_id']);

            // Hapus foreign key comment_id
            $table->dropForeign(['comment_id']);

            // Hapus kolom setelah foreign key-nya di-drop
            $table->dropColumn(['thread_id', 'comment_id']);
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Tambahkan kembali kolom dan foreign key
            $table->foreignId('thread_id')->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->constrained()->onDelete('cascade');
        });
    }
};

