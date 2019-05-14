<?php namespace RainLab\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class SettingsAddMeta extends Migration
{
    public function up()
    {
        Schema::table('keios_multisite_settings', function ($table) {
            $table->json('meta');
        });
    }

    public function down()
    {
        Schema::table('keios_multisite_settings', function ($table) {
            $table->dropColumn('meta');
        });
    }

}
