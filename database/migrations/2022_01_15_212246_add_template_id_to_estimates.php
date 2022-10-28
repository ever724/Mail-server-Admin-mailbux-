<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemplateIdToEstimates extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('estimate_templates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn('template_id');
        });
    }
}
