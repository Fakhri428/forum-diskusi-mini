<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('thread_id');
    $table->unsignedBigInteger('user_id');
    $table->text('body');
    $table->timestamps();

    $table->foreign('thread_id')->references('id')->on('threads')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

}

public function down()
{
    Schema::table('comments', function (Blueprint $table) {
        $table->dropForeign(['parent_id']);
        $table->dropColumn('parent_id');
    });
}

};

