<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraOrdersFiledToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('subscription_order_id')->after('order_id')->nullable();
            $table->string('subscription_id')->after('order_id')->nullable();
            $table->string('total_tax')->after('price')->nullable();
            $table->string('formatted_total')->after('price')->nullable();
            $table->string('formatted_tax')->after('price')->nullable();
            $table->string('completed_date')->after('payment_status')->nullable();
            $table->string('complete_timezone')->after('payment_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('subscription_order_id');
            $table->dropColumn('subscription_id');
            $table->dropColumn('total_tax');
            $table->dropColumn('formatted_total');
            $table->dropColumn('formatted_tax');
            $table->dropColumn('completed_date');
            $table->dropColumn('complete_timezone');
        });
    }
}
