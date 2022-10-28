<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToPlanSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->string('user_id')->after('company_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
