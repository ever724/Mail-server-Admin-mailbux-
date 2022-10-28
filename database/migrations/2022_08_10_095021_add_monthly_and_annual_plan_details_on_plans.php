<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonthlyAndAnnualPlanDetailsOnPlans extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('paddle_id');
            $table->dropColumn('sales_price');
            $table->unsignedFloat('monthly_price')->default(0)->after('is_active');
            $table->unsignedFloat('annual_price')->default(0)->after('monthly_price');
            $table->unsignedFloat('monthly_sales_price')->default(0)->after('annual_price');
            $table->unsignedFloat('annual_sales_price')->default(0)->after('monthly_sales_price');
            $table->integer('paddle_monthly_id')->nullable();
            $table->integer('paddle_annual_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedBigInteger('price')->after('is_active')->nullable();
            $table->unsignedBigInteger('sales_price')->after('price')->nullable();
            $table->string('paddle_id')->after('order')->nullable();
            $table->dropColumn('monthly_price');
            $table->dropColumn('annual_price');
            $table->dropColumn('monthly_sales_price');
            $table->dropColumn('annual_sales_price');
            $table->dropColumn('paddle_monthly_id');
            $table->dropColumn('paddle_annual_id');
        });
    }
}
