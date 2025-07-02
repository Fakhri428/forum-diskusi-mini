<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThreadIdToVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('votes', function (Blueprint $table) {
            // Periksa apakah kolom sudah ada
            if (!Schema::hasColumn('votes', 'thread_id')) {
                // Tambahkan kolom thread_id sebagai foreign key
                $table->foreignId('thread_id')->nullable()->constrained()->onDelete('cascade');
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
        Schema::table('votes', function (Blueprint $table) {
            // Hapus foreign key constraint dan kolom
            if (Schema::hasColumn('votes', 'thread_id')) {
                $table->dropForeign(['thread_id']);
                $table->dropColumn('thread_id');
            }
        });
    }
}
