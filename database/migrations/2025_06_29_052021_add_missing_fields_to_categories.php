<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Add each missing column with proper checks
            if (!Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }

            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('description');
            }

            if (!Schema::hasColumn('categories', 'color')) {
                $table->string('color')->nullable()->after('icon');
            }

            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('color');
            }

            if (!Schema::hasColumn('categories', 'position')) {
                $table->integer('position')->default(0)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Only drop columns if they exist
            $columns = ['description', 'slug', 'icon', 'color', 'is_active', 'position'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
