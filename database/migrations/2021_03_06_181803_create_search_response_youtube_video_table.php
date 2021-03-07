<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchResponseYoutubeVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_response_youtube_video', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('response_order');
            $table->foreignId('search_response_id')->constrained();
            $table->foreignId('youtube_video_id')->constrained();
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
        Schema::dropIfExists('search_response_youtube_video');
    }
}
