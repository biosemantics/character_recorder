<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnInMultipleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('values', function (Blueprint $table) {
            $table->boolean('not_remove')->after('value')->nullable();
        });
        Schema::table('color_details', function (Blueprint $table) {
            $table->boolean('not_remove')->after('post_constraint')->nullable();
        });
        Schema::table('non_color_details', function (Blueprint $table) {
            $table->boolean('not_remove')->after('post_constraint')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('values', function (Blueprint $table) {
            $table->dropColumn('not_remove');
        });
        Schema::table('color_details', function (Blueprint $table) {
            $table->dropColumn('not_remove');
        });
        Schema::table('non_color_details', function (Blueprint $table) {
            $table->dropColumn('not_remove');
        });
    }
}
