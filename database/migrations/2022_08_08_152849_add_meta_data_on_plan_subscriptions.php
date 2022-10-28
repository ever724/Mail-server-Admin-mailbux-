<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetaDataOnPlanSubscriptions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->text('meta_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->dropColumn('meta_data');
        });
    }
}
