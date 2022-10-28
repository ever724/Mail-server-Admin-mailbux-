<?php

use App\Models\SubscriptionInvoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusFieldOnSubscriptionInvoices extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('subscription_invoices', function (Blueprint $table) {
            $table->unsignedTinyInteger('payment_status')
                ->default(SubscriptionInvoice::PAYMENT_STATUS_SUCCESS)
                ->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('subscription_invoices', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
}
