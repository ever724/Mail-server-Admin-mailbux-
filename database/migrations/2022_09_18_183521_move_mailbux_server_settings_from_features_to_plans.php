<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveMailbuxServerSettingsFromFeaturesToPlans extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->json('mailbux_settings');
        });

        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropColumn('mailbux_feature_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('mailbux_settings');
        });

        Schema::table('plan_features', function (Blueprint $table) {
            $table->string('mailbux_feature_code')->default('');
        });
    }
}
