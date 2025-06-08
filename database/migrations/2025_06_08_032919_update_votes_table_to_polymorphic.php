<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Add new polymorphic columns
            $table->unsignedBigInteger('votable_id')->nullable();
            $table->string('votable_type')->nullable();

            // Create indexes for better performance
            $table->index(['votable_id', 'votable_type']);
        });

        // Transfer existing data if columns exist
        if (Schema::hasColumn('votes', 'thread_id')) {
            DB::statement('UPDATE votes SET votable_id = thread_id, votable_type = "App\\\\Models\\\\Thread" WHERE thread_id IS NOT NULL');
        }

        if (Schema::hasColumn('votes', 'comment_id')) {
            DB::statement('UPDATE votes SET votable_id = comment_id, votable_type = "App\\\\Models\\\\Comment" WHERE comment_id IS NOT NULL');
        }

        // Only drop old columns if they exist
        Schema::table('votes', function (Blueprint $table) {
            if (Schema::hasColumn('votes', 'thread_id')) {
                $table->dropColumn('thread_id');
            }

            if (Schema::hasColumn('votes', 'comment_id')) {
                $table->dropColumn('comment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Add back the original columns
            $table->unsignedBigInteger('thread_id')->nullable();
            $table->unsignedBigInteger('comment_id')->nullable();

            // Transfer data back
            DB::statement('UPDATE votes SET thread_id = votable_id WHERE votable_type = "App\\\\Models\\\\Thread"');
            DB::statement('UPDATE votes SET comment_id = votable_id WHERE votable_type = "App\\\\Models\\\\Comment"');

            // Drop the polymorphic columns
            $table->dropColumn(['votable_id', 'votable_type']);
        });
    }
};
