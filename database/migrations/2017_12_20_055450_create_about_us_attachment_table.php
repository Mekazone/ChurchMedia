<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAboutUsAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('about_us_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('fileCategoryId');
            $table->foreign('fileCategoryId')->references('id')->on('filecategories');
            $table->string('filePosition');
            $table->string('slug');
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
        Schema::dropIfExists('aboutUsAttachments');
    }
}
