<?php

namespace Winter\Mall\Classes\Registration;

use Backend\Facades\Backend;
use Winter\Mall\Models\FeedSettings;
use Winter\Mall\Models\GeneralSettings;
use Winter\Mall\Models\PaymentGatewaySettings;
use Winter\Mall\Models\ReviewSettings;

trait BootSettings
{
    public function registerSettings()
    {
        return [
            'general_settings'          => [
                'label'       => 'winter.mall::lang.general_settings.label',
                'description' => 'winter.mall::lang.general_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category',
                'icon'        => 'icon-shopping-cart',
                'class'       => GeneralSettings::class,
                'order'       => 0,
                'permissions' => ['winter.mall.settings.manage_general'],
                'keywords'    => 'shop store mall general',
            ],
            'currency_settings'         => [
                'label'       => 'winter.mall::lang.currency_settings.label',
                'description' => 'winter.mall::lang.currency_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category',
                'icon'        => 'icon-money',
                'url'         => Backend::url('winter/mall/currencies'),
                'order'       => 20,
                'permissions' => ['winter.mall.settings.manage_currency'],
                'keywords'    => 'shop store mall currency',
            ],
            'price_categories_settings' => [
                'label'       => 'winter.mall::lang.price_category_settings.label',
                'description' => 'winter.mall::lang.price_category_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category',
                'icon'        => 'icon-pie-chart',
                'url'         => Backend::url('winter/mall/pricecategories'),
                'order'       => 20,
                'permissions' => ['winter.mall.settings.manage_price_categories'],
                'keywords'    => 'shop store mall currency price categories',
            ],
            'tax_settings'              => [
                'label'       => 'winter.mall::lang.common.taxes',
                'description' => 'winter.mall::lang.tax_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category',
                'icon'        => 'icon-percent',
                'url'         => Backend::url('winter/mall/taxes'),
                'order'       => 40,
                'permissions' => ['winter.mall.manage_taxes'],
                'keywords'    => 'shop store mall tax taxes',
            ],
            'notification_settings'     => [
                'label'       => 'winter.mall::lang.notification_settings.label',
                'description' => 'winter.mall::lang.notification_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category',
                'icon'        => 'icon-envelope',
                'url'         => Backend::url('winter/mall/notifications'),
                'order'       => 40,
                'permissions' => ['winter.mall.manage_notifications'],
                'keywords'    => 'shop store mall notifications email mail',
            ],
            'feed_settings'             => [
                'label'       => 'winter.mall::lang.common.feeds',
                'description' => 'winter.mall::lang.feed_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category',
                'icon'        => 'icon-rss',
                'class'       => FeedSettings::class,
                'order'       => 50,
                'permissions' => ['winter.mall.manage_feeds'],
                'keywords'    => 'shop store mall feeds',
            ],
            'review_settings'           => [
                'label'       => 'winter.mall::lang.common.reviews',
                'description' => 'winter.mall::lang.review_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category',
                'icon'        => 'icon-star',
                'class'       => ReviewSettings::class,
                'order'       => 60,
                'permissions' => ['winter.mall.manage_reviews'],
                'keywords'    => 'shop store mall reviews',
            ],
            'payment_gateways_settings' => [
                'label'       => 'winter.mall::lang.payment_gateway_settings.label',
                'description' => 'winter.mall::lang.payment_gateway_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category_payments',
                'icon'        => 'icon-credit-card',
                'class'       => PaymentGatewaySettings::class,
                'order'       => 30,
                'permissions' => ['winter.mall.settings.manage_payment_gateways'],
                'keywords'    => 'shop store mall payment gateways',
            ],
            'payment_method_settings'   => [
                'label'       => 'winter.mall::lang.common.payment_methods',
                'description' => 'winter.mall::lang.payment_method_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category_payments',
                'icon'        => 'icon-money',
                'url'         => Backend::url('winter/mall/paymentmethods'),
                'order'       => 40,
                'permissions' => ['winter.mall.settings.manage_payment_methods'],
                'keywords'    => 'shop store mall payment methods',
            ],
            'shipping_method_settings'  => [
                'label'       => 'winter.mall::lang.common.shipping_methods',
                'description' => 'winter.mall::lang.shipping_method_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category_orders',
                'icon'        => 'icon-truck',
                'url'         => Backend::url('winter/mall/shippingmethods'),
                'order'       => 40,
                'permissions' => ['winter.mall.manage_shipping_methods'],
                'keywords'    => 'shop store mall shipping methods',
            ],
            'order_state_settings'      => [
                'label'       => 'winter.mall::lang.common.order_states',
                'description' => 'winter.mall::lang.order_state_settings.description',
                'category'    => 'winter.mall::lang.general_settings.category_orders',
                'icon'        => 'icon-history',
                'url'         => Backend::url('winter/mall/orderstate'),
                'order'       => 50,
                'permissions' => ['winter.mall.manage_order_states'],
                'keywords'    => 'shop store mall notifications email mail',
            ],
        ];
    }
}
