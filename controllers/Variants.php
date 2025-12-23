<?php namespace Winter\Mall\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use Winter\Mall\Models\GeneralSettings;

class Variants extends Controller
{
    public $implement = [
        ListController::class,
        FormController::class,
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'winter.mall.manage_products',
    ];

    public function formExtendFields($widget)
    {
        if (!GeneralSettings::get('use_orders', true)) {
            $widget->removeTab('winter.mall::lang.common.shipping');
        }
    }
}
