<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInspirationalCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspirational_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('postId');
            $table->foreign('postId')->references('id')->on('inspirationals');
            $table->string('body');
            $table->string('commenter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspirational_comments');
    }
}