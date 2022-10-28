<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketMessageAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ticket_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id');
            $table->string('file_name');
            $table->integer('size');
            $table->string('storage_disk');
            $table->string('storage_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ticket_message_attachments');
    }
}
