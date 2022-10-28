<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Activitylog\Models\Activity;

class FixViewedAttributeForInvoices extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        $activities = Activity::where('causer_type', 'App\Models\Invoice')->where('description', 'viewed')->get();
        foreach ($activities as $activity) {
            if ($invoice = $activity->causer) {
                $invoice->update(['viewed' => true]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
    }
}
