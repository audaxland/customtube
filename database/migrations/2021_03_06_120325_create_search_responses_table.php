<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('search_request_id');
            $table->string('etag');
            $table->string('next_page_token')->nullable();
            $table->string('region_code');
            $table->unsignedInteger('total_results')->default(0);
            $table->unsignedInteger('results_per_page')->default(0);
            $table->unsignedInteger('current_list_count')->default(0);
            $table->unsignedInteger('result_order_first')->default(1);
            $table->unsignedInteger('result_order_last')->default(1);
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
        Schema::dropIfExists('search_responses');
    }
}
