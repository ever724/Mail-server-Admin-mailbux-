<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainAdminsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('domain_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id');
            $table->string('username');
            $table->string('password');
            $table->boolean('api_access');
            $table->boolean('enabled');
            $table->string('recovery_email');
            $table->string('language');
            $table->string('domains');
            $table->integer('storagequota_total');
            $table->integer('quota_domains');
            $table->integer('quota_mailboxes');
            $table->integer('quota_aliases');
            $table->integer('quota_domainaliases');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('domain_admins');
    }
}
