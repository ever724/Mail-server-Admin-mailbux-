<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->bigInteger('client_id');
            $table->float('amount');
            $table->string('currency');
            $table->string('country');
            $table->dateTime('paid_at')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->float('next_payment_amount')->nullable();
            $table->boolean('is_first_payment');
            $table->string('payment_method')->default('unknown');
            $table->string('status')->default('unknown');
            $table->string('paddle_subscription_id');
            $table->integer('paddle_plan_id');
            $table->string('paddle_checkout_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('subscription_invoices');
    }
}
