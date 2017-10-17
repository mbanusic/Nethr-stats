<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category');
	        $table->integer('chars');
	        $table->integer('posts');
	        $table->integer('images');
	        $table->integer('day');
	        $table->integer('month');
	        $table->integer('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_stats');
    }
}
