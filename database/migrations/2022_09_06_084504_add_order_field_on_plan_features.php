<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderFieldOnPlanFeatures extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->unsignedInteger('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
