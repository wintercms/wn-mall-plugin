<?php namespace Winter\Mall\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use Illuminate\Support\Facades\Cache;
use Winter\Storm\Support\Facades\Flash;
use Winter\Mall\Models\Notification;
use System\Classes\SettingsManager;

class Notifications extends Controller
{
    public $implement = [
        ListController::class,
        FormController::class,
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'winter.mall.manage_notifications',
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Winter.Mall', 'notification_settings');
    }

    public function onToggleClicked()
    {
        $action = post('action');
        if ($action === 'disable') {
            $value = 0;
        } elseif ($action === 'enable') {
            $value = 1;
        } else {
            return;
        }

        Notification::whereIn('id', post('checked'))->update(['enabled' => $value]);
        Cache::forget(Notification::CACHE_KEY);

        Flash::success(trans('backend::lang.form.update_success', ['name' => trans('winter.mall::lang.common.notification')
        ]));
    }
}
