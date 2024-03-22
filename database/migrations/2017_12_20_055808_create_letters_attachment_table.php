<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLettersAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('letters_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('fileCategoryId');
            $table->foreign('fileCategoryId')->references('id')->on('file_categories');
            $table->string('filePosition');
            $table->string('slug');
            $table->integer('postId');
            $table->foreign('postId')->references('id')->on('letters');
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
        Schema::dropIfExists('letters_attachments');
    }
}
