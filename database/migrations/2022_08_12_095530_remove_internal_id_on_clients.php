<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveInternalIdOnClients extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('external_id');
            $table->dropColumn('id');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->bigInteger('id');
        });

        DB::table('clients')->truncate();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedInteger('external_id');
        });
    }
}
