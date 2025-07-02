<?php
// filepath: database/migrations/xxxx_xx_xx_add_missing_columns_to_comments_table.php

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
        Schema::table('comments', function (Blueprint $table) {
            // Add is_approved column if it doesn't exist
            if (!Schema::hasColumn('comments', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('body');
            }

            // Add vote_score column if it doesn't exist
            if (!Schema::hasColumn('comments', 'vote_score')) {
                $table->integer('vote_score')->default(0)->after('is_approved');
            }

            // Add soft deletes if it doesn't exist
            if (!Schema::hasColumn('comments', 'deleted_at')) {
                $table->softDeletes();
            }

            // Add indexes for better performance
            $table->index('is_approved');
            $table->index(['thread_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['is_approved', 'vote_score']);
            $table->dropSoftDeletes();
            $table->dropIndex(['comments_is_approved_index']);
            $table->dropIndex(['comments_thread_id_parent_id_index']);
        });
    }
};
