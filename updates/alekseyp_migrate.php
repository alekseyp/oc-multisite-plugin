<?php namespace Alekseyp\Multisite\Seeds;

use Seeder;
use System\Classes\PluginManager;

/**
 * Class AlekseyPMigrate
 * @package Alekseyp\Multisite\Seeds
 */
class AlekseypMigrate extends Seeder
{
    /**
     * Migrates old table to new
     */
    public function run()
    {
        if (\Schema::hasTable('keios_multisite_settings')) {
            $rows = \DB::table('keios_multisite_settings')->get(
                [
                    'domain',
                    'theme',
                    'is_protected',
                    'created_at',
                    'updated_at',
                ]
            );

            $data = [];
            foreach ($rows as $row) {
                $data[] = get_object_vars($row);
            }

            \DB::table('alekseyp_multisite_settings')->insert($data);

            if (PluginManager::instance()->exists('Keios.Multisite')) {
                \Artisan::call('plugin:remove', ['name' => 'Keios.Multisite', '--force' => true]);
            }

            \Schema::dropIfExists('keios_multisite_settings');
        }

    }
}
