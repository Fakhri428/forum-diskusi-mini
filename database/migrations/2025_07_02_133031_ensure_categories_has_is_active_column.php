<?php
// filepath: database/migrations/xxxx_xx_xx_ensure_categories_has_is_active_column.php

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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('name');
            }
            if (!Schema::hasColumn('categories', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('sort_order');
            }
            if (!Schema::hasColumn('categories', 'color')) {
                $table->string('color')->default('#007bff')->after('description');
            }
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('color');
            }
            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }
        });

        // Update existing categories to have slugs
        $categories = \App\Models\Category::whereNull('slug')->get();
        foreach ($categories as $category) {
            $category->slug = \Illuminate\Support\Str::slug($category->name);
            $category->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $columns = ['is_active', 'sort_order', 'description', 'color', 'icon', 'slug'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
