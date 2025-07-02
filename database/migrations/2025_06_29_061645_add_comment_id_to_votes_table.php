<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentIdToVotesTable extends Migration
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
            if (!Schema::hasColumn('votes', 'comment_id')) {
                // Tambahkan kolom comment_id sebagai foreign key
                $table->foreignId('comment_id')->nullable()->constrained()->onDelete('cascade');
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
            if (Schema::hasColumn('votes', 'comment_id')) {
                $table->dropForeign(['comment_id']);
                $table->dropColumn('comment_id');
            }
        });
    }
}
