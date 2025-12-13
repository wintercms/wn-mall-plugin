<?php

namespace Winter\Mall\Classes\Registration;

use Event;
use Winter\Mall\Classes\Events\MailingEventHandler;
use Winter\Mall\Classes\Search\ProductsSearchProvider;
use Winter\Mall\Models\Brand;
use Winter\Mall\Models\Cart;
use Winter\Mall\Models\Category;
use Winter\Mall\Models\Customer;
use Winter\Mall\Models\GeneralSettings;
use Winter\Mall\Models\Order;
use Winter\Mall\Models\Product;
use Winter\Mall\Models\ProductPrice;
use Winter\Mall\Models\PropertyValue;
use Winter\Mall\Models\Variant;
use Winter\Mall\Models\Wishlist;

trait BootEvents
{
    protected function registerEvents()
    {
        $this->registerObservers();
        $this->registerGenericEvents();
        $this->registerStaticPagesEvents();
        $this->registerSiteSearchEvents();
        $this->registerGdprEvents();
    }

    public function registerObservers()
    {
        Product::observe(\Winter\Mall\Classes\Observers\ProductObserver::class);
        Variant::observe(\Winter\Mall\Classes\Observers\VariantObserver::class);
        Brand::observe(\Winter\Mall\Classes\Observers\BrandObserver::class);
        PropertyValue::observe(\Winter\Mall\Classes\Observers\PropertyValueObserver::class);
        ProductPrice::observe(\Winter\Mall\Classes\Observers\ProductPriceObserver::class);
    }

    protected function registerGenericEvents()
    {
        $this->app->bind('MailingEventHandler', MailingEventHandler::class);
        $this->app['events']->subscribe('MailingEventHandler');
    }

    protected function registerStaticPagesEvents()
    {
        Event::listen('pages.menuitem.listTypes', function () {
            return [
                'mall-category'       => '[Winter.Mall] ' . trans('winter.mall::lang.menu_items.single_category'),
                'all-mall-categories' => '[Winter.Mall] ' . trans('winter.mall::lang.menu_items.all_categories'),
                'all-mall-products'   => '[Winter.Mall] ' . trans('winter.mall::lang.menu_items.all_products'),
                'all-mall-variants'   => '[Winter.Mall] ' . trans('winter.mall::lang.menu_items.all_variants'),
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function ($type) {
            if ($type === 'all-mall-categories' || $type === 'mall-category') {
                return Category::getMenuTypeInfo($type);
            }
            if ($type === 'all-mall-products' || $type === 'all-mall-variants') {
                return [
                    'dynamicItems' => true,
                ];
            }
        });

        Event::listen('pages.menuitem.resolveItem', function ($type, $item, $url, $theme) {
            if ($type === 'all-mall-categories') {
                return Category::resolveCategoriesItem($item, $url, $theme);
            }
            if ($type === 'mall-category') {
                return Category::resolveCategoryItem($item, $url, $theme);
            }
            if ($type === 'all-mall-products') {
                return Product::resolveItem($item, $url, $theme);
            }
            if ($type === 'all-mall-variants') {
                return Variant::resolveItem($item, $url, $theme);
            }
        });

        // Translate slugs
        Event::listen('translate.localePicker.translateParams', function ($page, $params, $oldLocale, $newLocale) {
            if ($page->getBaseFileName() === GeneralSettings::get('category_page')) {
                return Category::translateParams($params, $oldLocale, $newLocale);
            }
            if ($page->getBaseFileName() === GeneralSettings::get('product_page')) {
                return Product::translateParams($params, $oldLocale, $newLocale);
            }
        });
    }

    protected function registerSiteSearchEvents()
    {
        Event::listen('offline.sitesearch.extend', function () {
            return new ProductsSearchProvider();
        });
    }

    protected function registerGdprEvents()
    {
        Event::listen('offline.gdpr::cleanup.register', function () {
            return [
                'id'     => 'oc-mall-plugin',
                'label'  => 'Winter Mall',
                'models' => [
                    [
                        'label'   => 'Customers',
                        'comment' => 'Delete inactive customer accounts (based on last login date)',
                        'class'   => Customer::class,
                    ],
                    [
                        'label'   => 'Orders',
                        'comment' => 'Delete completed orders',
                        'class'   => Order::class,
                    ],
                    [
                        'label'   => 'Carts',
                        'comment' => 'Delete abandoned shopping carts',
                        'class'   => Cart::class,
                    ],
                    [
                        'label'   => 'Wishlists',
                        'comment' => 'Delete old wishlists of unregistered users',
                        'class'   => Wishlist::class,
                    ],
                ],
            ];
        });
    }
}
