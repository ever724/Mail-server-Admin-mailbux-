<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMailbuxServerFeatureCodeOnPlanFeatures extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->string('mailbux_feature_code')->default('');
            $table->boolean('is_displayed')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropColumn('mailbux_feature_code');
            $table->dropColumn('is_displayed');
        });
    }
}
