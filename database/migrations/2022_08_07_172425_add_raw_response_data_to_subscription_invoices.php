<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRawResponseDataToSubscriptionInvoices extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('subscription_invoices', function (Blueprint $table) {
            $table->text('response_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('subscription_invoices', function (Blueprint $table) {
            $table->removeColumn('response_data');
        });
    }
}
