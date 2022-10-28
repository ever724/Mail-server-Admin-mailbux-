<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaddleSubscriptionIdToPlanSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->string('subscription_id')->after('plan_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->dropColumn('subscription_id');
        });
    }
}
