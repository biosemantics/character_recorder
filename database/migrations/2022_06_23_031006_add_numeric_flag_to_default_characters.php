<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNumericFlagToDefaultCharacters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_characters', function (Blueprint $table) {
            $table->boolean('numeric_flag')->after('images')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('default_characters', function (Blueprint $table) {
            $table->dropColumn('numeric_flag');
        });
    }
}
