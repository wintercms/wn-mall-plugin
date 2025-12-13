<?php namespace Winter\Mall\Components;

use Cms\Classes\ComponentBase;

class EnhancedEcommerceAnalytics extends ComponentBase
{
    public $products;

    public function componentDetails()
    {
        return [
            'name'        => 'winter.mall::lang.components.enhancedEcommerceAnalytics.details.name',
            'description' => 'winter.mall::lang.components.enhancedEcommerceAnalytics.details.description',
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function init()
    {
    }
}
