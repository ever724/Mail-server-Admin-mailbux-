<?php

use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class UpdateV106 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Call composer dump-autoload
        Artisan::call('dump:autoload');

        SystemSetting::setSetting('version', '1.0.6');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        SystemSetting::setSetting('version', '1.0.5');
    }
}
