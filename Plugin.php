<?php namespace Winter\Mall;


use Backend;
use Illuminate\Support\Facades\View;
use Winter\Mall\Classes\Registration\BootComponents;
use Winter\Mall\Classes\Registration\BootEvents;
use Winter\Mall\Classes\Registration\BootExtensions;
use Winter\Mall\Classes\Registration\BootMails;
use Winter\Mall\Classes\Registration\BootRelations;
use Winter\Mall\Classes\Registration\BootServiceContainer;
use Winter\Mall\Classes\Registration\BootSettings;
use Winter\Mall\Classes\Registration\BootTwig;
use Winter\Mall\Classes\Registration\BootValidation;
use Winter\Mall\Console\Initialize;
use Winter\Mall\Console\ReindexProducts;
use Winter\Mall\Console\SeedDemoData;
use Winter\Mall\Console\SystemCheck;
use Winter\Mall\Models\GeneralSettings;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['Winter.User', 'Winter.Location', 'Winter.Translate'];

    use BootEvents;
    use BootExtensions;
    use BootServiceContainer;
    use BootSettings;
    use BootComponents;
    use BootMails;
    use BootValidation;
    use BootTwig;
    use BootRelations;

    public function __construct($app)
    {
        parent::__construct($app);
        // The morph map has to be registered in the constructor so it is available
        // when plugin migrations are run.
        $this->registerRelations();
    }

    public function pluginDetails()
    {
        return [
            'name' => 'winter.mall::lang.plugin.name',
            'description' => 'winter.mall::lang.plugin.description',
            'author' => 'OFFLINE GmbH',
            'icon' => 'oc-icon-shopping-cart',
            'homepage' => '',
            'replaces' => ['OFFLINE.Mall' => '~2.0'],
        ];
    }

    public function register()
    {
        $this->registerServices();
        $this->registerTwigEnvironment();
    }

    public function boot()
    {
        $this->registerExtensions();
        $this->registerEvents();
        $this->registerValidationRules();

        $this->registerConsoleCommand('winter.mall.seed-demo', SeedDemoData::class);
        $this->registerConsoleCommand('winter.mall.reindex', ReindexProducts::class);
        $this->registerConsoleCommand('winter.mall.system-check', SystemCheck::class);
        $this->registerConsoleCommand('winter.mall.initialize', Initialize::class);

        View::share('app_url', config('app.url'));
    }

    public function registerNavigation()
    {
        $result = [];
        $result['mall-catalogue'] = [
            'label' => 'winter.mall::lang.common.catalogue',
            'url' => Backend::url('winter/mall/products'),
            'icon' => 'icon-book',
            'order' => 800,
            'permissions' => [
                'winter.mall.manage_products',
            ],
            'sideMenu' => [
                'mall-products' => [
                    'label' => 'winter.mall::lang.common.products',
                    'url' => Backend::url('winter/mall/products'),
                    'icon' => 'icon-cart-plus',
                    'order' => 100,
                    'permissions' => [
                        'winter.mall.manage_products',
                    ],
                ],
                'mall-categories' => [
                    'label' => 'winter.mall::lang.common.categories',
                    'url' => Backend::url('winter/mall/categories'),
                    'icon' => 'icon-sitemap',
                    'order' => 400,
                    'permissions' => [
                        'winter.mall.manage_categories',
                    ],
                ],
                'mall-properties' => [
                    'label' => 'winter.mall::lang.common.properties',
                    'url' => Backend::url('winter/mall/propertygroups'),
                    'icon' => 'icon-tags',
                    'order' => 600,
                    'permissions' => [
                        'winter.mall.manage_properties',
                    ],
                ],
            ],
        ];
        if (GeneralSettings::get('use_reviews', true)) {
            $result['mall-catalogue']['sideMenu']['mall-reviews'] = [
                'label' => 'winter.mall::lang.common.reviews',
                'url' => Backend::url('winter/mall/reviews'),
                'icon' => 'icon-star-half-full',
                'order' => 200,
                'permissions' => [
                    'winter.mall.manage_reviews',
                ],
            ];
        }
        if (GeneralSettings::get('use_services', true)) {
            $result['mall-catalogue']['sideMenu']['mall-services'] = [
                'label' => 'winter.mall::lang.common.services',
                'url' => Backend::url('winter/mall/services'),
                'icon' => 'icon-plus-circle',
                'order' => 300,
                'permissions' => [
                    'winter.mall.manage_services',
                ],
            ];
        }
        if (GeneralSettings::get('use_brands', true)) {
            $result['mall-catalogue']['sideMenu']['mall-brands'] = [
                'label' => 'winter.mall::lang.common.brands',
                'url' => Backend::url('winter/mall/brands'),
                'icon' => 'icon-cube',
                'order' => 500,
                'permissions' => [
                    'winter.mall.manage_brands',
                ],
            ];
        }
        if (GeneralSettings::get('use_orders', true)) {
            $result['mall-orders'] = [
                'label' => 'winter.mall::lang.common.orders',
                'url' => Backend::url('winter/mall/orders'),
                'icon' => 'icon-shopping-cart',
                'order' => 801,
                'permissions' => [
                    'winter.mall.*',
                ],
                'sideMenu' => [
                    'mall-orders' => [
                        'label' => 'winter.mall::lang.common.orders',
                        'url' => Backend::url('winter/mall/orders'),
                        'icon' => 'icon-check-circle',
                        'permissions' => [
                            'winter.mall.manage_orders',
                        ],
                    ],
                    'mall-discounts' => [
                        'label' => 'winter.mall::lang.common.discounts',
                        'url' => Backend::url('winter/mall/discounts'),
                        'icon' => 'icon-gift',
                        'permissions' => [
                            'winter.mall.manage_discounts',
                        ],
                    ],
                    'mall-payment-log' => [
                        'label' => 'winter.mall::lang.common.payments',
                        'url' => Backend::url('winter/mall/paymentlogs'),
                        'icon' => 'icon-money',
                        'permissions' => [
                            'winter.mall.manage_payment_log'
                        ],
                    ],
                ],
            ];
        }

        return $result;
    }
}
