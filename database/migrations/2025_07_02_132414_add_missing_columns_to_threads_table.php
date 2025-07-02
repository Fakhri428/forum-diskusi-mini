<?php
// filepath: database/migrations/xxxx_xx_xx_add_missing_columns_to_threads_table.php

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
            // Cek dan tambahkan kolom yang hilang
            if (!Schema::hasColumn('threads', 'tags')) {
                $table->string('tags')->nullable()->after('body');
            }
            if (!Schema::hasColumn('threads', 'image')) {
                $table->string('image')->nullable()->after('tags');
            }
            if (!Schema::hasColumn('threads', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('user_id');
            }
            if (!Schema::hasColumn('threads', 'views_count')) {
                $table->integer('views_count')->default(0)->after('is_approved');
            }
            if (!Schema::hasColumn('threads', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('views_count');
            }
            if (!Schema::hasColumn('threads', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('is_pinned');
            }
            if (!Schema::hasColumn('threads', 'vote_score')) {
                $table->integer('vote_score')->default(0)->after('is_locked');
            }
            if (!Schema::hasColumn('threads', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $columns = ['tags', 'image', 'is_approved', 'views_count', 'is_pinned', 'is_locked', 'vote_score', 'deleted_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('threads', $column)) {
                    if ($column === 'deleted_at') {
                        $table->dropSoftDeletes();
                    } else {
                        $table->dropColumn($column);
                    }
                }
            }
        });
    }
};
