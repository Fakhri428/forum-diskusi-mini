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
        Schema::table('threads', function (Blueprint $table) {
            if (!Schema::hasColumn('threads', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false);
            }
            if (!Schema::hasColumn('threads', 'is_locked')) {
                $table->boolean('is_locked')->default(false);
            }
            if (!Schema::hasColumn('threads', 'is_approved')) {
                $table->boolean('is_approved')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'is_locked', 'is_approved']);
        });
    }
};
