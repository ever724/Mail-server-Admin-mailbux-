<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsIntoClients extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('api_access')->default(true);
            $table->boolean('enabled')->default(true);
            $table->string('domain')->nullable();
            $table->string('language', 5)->default('en');
            $table->dateTime('last_login')->nullable();
            $table->integer('storagequota_total')->default(0);
            $table->integer('storagequota_used')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('api_access');
            $table->dropColumn('enabled');
            $table->dropColumn('domain');
            $table->dropColumn('language');
            $table->dropColumn('last_login');
            $table->dropColumn('storagequota_total');
            $table->dropColumn('storagequota_used');
        });
    }
}
