<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNonColorDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_color_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('value_id');
            $table->string('negation')->nullable();
            $table->string('pre_constraint')->nullable();
            $table->string('certainty_constraint')->nullable();
            $table->string('degree_constraint')->nullable();
            $table->string('main_value')->nullable();
            $table->string('post_constraint')->nullable();
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
        Schema::dropIfExists('non_color_details');
    }
}
