<?php namespace Voipdeploy\Multisite;

use System\Classes\PluginBase;
use Voipdeploy\Multisite\Models\Setting;
use Backend;
use Config;
use Event;
use Cache;
use Request;
use App;

/**
 * Multisite Plugin Information File
 * Plugin icon is used with Creative Commons (CC BY 4.0) Licence
 * Icon author: http://pixelkit.com/
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Multisite',
            'description' => 'Multisite support plugin for October',
            'author' => 'VoipDeploy',
            'icon' => 'icon-cubes'
        ];
    }

    public function registerSettings()
    {
        return [
            'multisite' => [
                'label' => 'Multisite',
                'description' => 'Manage multisite domain to theme binds.',
                'category' => 'system::lang.system.categories.cms',
                'icon' => 'icon-cubes',
                'url' => Backend::url('voipdeploy/multisite/settings'),
                'order' => 500,
                'keywords' => 'multisite domains themes'
            ]
        ];
    }

    public function boot()
    {
        $backendUri = Config::get('cms.backendUri');
        $requestUrl = Request::url();
        $currentHostUrl = Request::getHost();

        /*
         * Get domain to theme bindings from cache, if it's not there, load them from database,
         * save to cache and use for theme selection.
         */
        $binds = Cache::rememberForever('voipdeploy_multisite_settings', function () {

            $cacheableRecords = Setting::generateCacheableRecords();

            return $cacheableRecords;

        });

        /*
         * Check if this request is in backend scope and is using domain,
         * that is protected from using backend
         */
        foreach ($binds as $domain => $bind) {
            if (preg_match('/\\' . $backendUri . '/', $requestUrl) && preg_match('/' . $currentHostUrl . '/i', $domain) && $bind['is_protected']) {
                return App::abort(401, 'Unauthorized.');
            };
        }

        /*
         * If current request is in backend scope, do not check cms themes
         * Allows for current theme changes in October Theme Selector
         */
        if (preg_match('/\\' . $backendUri . '/', $requestUrl)) return;

        /*
         * Listen for CMS activeTheme event, change theme according to binds
         * If there's no match, let CMS set active theme
         */
        Event::listen('cms.activeTheme', function () use ($binds, $currentHostUrl) {
            foreach ($binds as $domain => $bind) {
                if (preg_match('/' . $currentHostUrl . '/i', $domain)) {
                    Config::set('app.url', $domain);
                    return $bind['theme'];
                }
            }
        });
    }

}
