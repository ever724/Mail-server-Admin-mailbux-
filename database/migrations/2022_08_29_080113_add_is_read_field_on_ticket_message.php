<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReadFieldOnTicketMessage extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->boolean('is_read_by_admin')->default(false);
            $table->boolean('is_read_by_client')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropColumn('is_read_by_admin');
            $table->dropColumn('is_read_by_client');
        });
    }
}
