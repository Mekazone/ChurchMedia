<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBishopsEventsCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bishops_events_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('postId');
            $table->foreign('postId')->references('id')->on('bishop_events');
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
        Schema::dropIfExists('bishops_events_comments');
    }
}
