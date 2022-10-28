<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserIdToClientIdOnPlanSubscriptions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->renameColumn('user_id', 'client_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('user_id', 'client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->renameColumn('client_id', 'user_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('client_id', 'user_id');
        });
    }
}
