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
        Schema::create('moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // approve_threads, reject_threads, etc.
            $table->text('content');
            $table->json('details')->nullable(); // Additional data
            $table->timestamps();

            // Indexes
            $table->index('moderator_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderation_logs');
    }
};
