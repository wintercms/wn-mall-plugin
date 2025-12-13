<?php namespace Winter\Mall\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\RelationController;
use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Winter\Mall\Classes\Traits\ReorderRelation;
use Winter\Mall\Models\Price;
use Winter\Mall\Models\Service;

class Services extends Controller
{
    use ReorderRelation;

    public $implement = [ListController::class, FormController::class, RelationController::class];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'winter.mall.manage_services',
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Winter.Mall', 'mall-catalogue', 'mall-services');

        // Legacy (v1)
        if (!class_exists('System')) {
            $this->addJs('/plugins/winter/mall/assets/Sortable.js');
        }

        $this->addJs('/plugins/winter/mall/assets/backend.js');
    }

    public function onRelationManageCreate()
    {
        $parent = parent::onRelationManageCreate();

        // Store the pricing information with the custom fields.
        if ($this->relationName === 'options') {
            $this->updatePrices($this->relationModel, '_prices');
        }

        return $parent;
    }

    public function onRelationManageUpdate()
    {
        $parent = parent::onRelationManageUpdate();

        // Store the pricing information with the custom fields.
        if ($this->relationName === 'options') {
            $model = $this->relationModel->find($this->vars['relationManageId']);
            $this->updatePrices($model, '_prices');
        }

        return $parent;
    }

    protected function updatePrices($model, $key = 'prices')
    {
        $data = post('MallPrice', []);
        \DB::transaction(function () use ($model, $key, $data) {
            foreach ($data as $currency => $_data) {
                $value = array_get($_data, $key);
                if ($value === '') {
                    $value = null;
                }

                Price::updateOrCreate([
                    'price_category_id' => null,
                    'priceable_id'      => $model->id,
                    'priceable_type'    => $model::MORPH_KEY,
                    'currency_id'       => $currency,
                ], [
                    'price' => $value,
                ]);
            }
        });
    }
}
