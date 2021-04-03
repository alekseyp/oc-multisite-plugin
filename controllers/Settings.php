<?php namespace Alekseyp\Multisite\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use Alekseyp\Multisite\Models\Setting;
use Cache;
use Flash;
use Lang;

/**
 * Settings Back-end Controller
 */
class Settings extends Controller
{
    /**
     * @var array
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
    ];

    /**
     * @var string
     */
    public $formConfig = 'config_form.yaml';
    /**
     * @var string
     */
    public $listConfig = 'config_list.yaml';

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('alekseyp.multisite', 'multisite');
    }

    /**
     * Multisite Settings Controller onDelete method
     *
     * @return mixed
     */
    public function onDelete()
    {
        $selected = post('checked');
        Setting::destroy($selected);

        return $this->listRefresh();
    }

    /**
     * Multisite Settings Controller onClearCache method
     */
    public function onClearCache()
    {
        Cache::forget('alekseyp_multisite_settings');
        Flash::success(Lang::get('alekseyp.multisite::lang.flash.cache-clear'));
    }
}
