<?php

use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;

class CreateSystemVersion extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        SystemSetting::setSetting('version', '1.0.2');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        SystemSetting::setSetting('version', '1.0.1');
    }
}
