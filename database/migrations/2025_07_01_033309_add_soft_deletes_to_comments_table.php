<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            // Tambahkan kolom deleted_at jika belum ada
            if (!Schema::hasColumn('comments', 'deleted_at')) {
                $table->softDeletes();
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
        Schema::table('comments', function (Blueprint $table) {
            // Hapus kolom deleted_at jika ada
            if (Schema::hasColumn('comments', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
}
