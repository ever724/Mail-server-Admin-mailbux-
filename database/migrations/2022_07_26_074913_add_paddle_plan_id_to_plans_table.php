<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaddlePlanIdToPlansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('paddle_id')->after('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('paddle_id');
        });
    }
}
