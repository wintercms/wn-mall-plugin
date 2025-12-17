<?php namespace Winter\Mall\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\RelationController;
use Backend\Behaviors\ReorderController;
use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Winter\Mall\Classes\Traits\ReorderRelation;
use Winter\Mall\Models\Category;
use Winter\Mall\Models\GeneralSettings;

class Categories extends Controller
{
    use ReorderRelation;

    public $implement = [
        ListController::class,
        FormController::class,
        ReorderController::class,
        RelationController::class,
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'winter.mall.manage_categories',
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Winter.Mall', 'mall-catalogue', 'mall-categories');

        // Legacy (v1)
        if (!class_exists('System')) {
            $this->addJs('/plugins/winter/mall/assets/Sortable.js');
        }

        $this->addJs('/plugins/winter/mall/assets/backend.js');
    }

    public function formExtendFields($widget)
    {
        if (!GeneralSettings::get('use_reviews', true)) {
            $widget->removeTab('winter.mall::lang.common.review_categories');
        }
    }

    public function onReorder()
    {
        parent::onReorder();
        (new Category())->purgeCache();
    }
}
