<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_requests', function (Blueprint $table) {
            $table->id();
            $table->string('query')->index();
            $table->unsignedInteger('total_results')->default(0);
            $table->unsignedInteger('fetched_results')->default(0);
            $table->string('last_page_token')->nullable();
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
        Schema::dropIfExists('search_requests');
    }
}
