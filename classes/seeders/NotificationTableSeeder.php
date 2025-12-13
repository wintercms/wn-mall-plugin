<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Models\Notification;

class NotificationTableSeeder extends Seeder
{
    public function run()
    {
        Notification::extend(function () {
            $this->setTable('offline_mall_notifications');
            $this->rules['code'] = str_replace('winter_', 'offline_', $this->rules['code']);
        }, true);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::admin.checkout_succeeded',
            'name'        => 'Admin notification: Checkout succeeded',
            'description' => 'Sent to the shop admin when a checkout succeeded',
            'template'    => 'winter.mall::mail.admin.checkout_succeeded',
        ]);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::admin.checkout_failed',
            'name'        => 'Admin notification: Checkout failed',
            'description' => 'Sent to the shop admin when a checkout failed',
            'template'    => 'winter.mall::mail.admin.checkout_failed',
        ]);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::customer.created',
            'name'        => 'Customer signed up',
            'description' => 'Sent when a customer has signed up',
            'template'    => 'winter.mall::mail.customer.created',
        ]);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::checkout.succeeded',
            'name'        => 'Checkout succeeded',
            'description' => 'Sent when a checkout was successfull',
            'template'    => 'winter.mall::mail.checkout.succeeded',
        ]);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::checkout.failed',
            'name'        => 'Checkout failed',
            'description' => 'Sent when a checkout has failed',
            'template'    => 'winter.mall::mail.checkout.failed',
        ]);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::order.shipped',
            'name'        => 'Order shipped',
            'description' => 'Sent when the order has been marked as shipped',
            'template'    => 'winter.mall::mail.order.shipped',
        ]);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::order.state.changed',
            'name'        => 'Order status changed',
            'description' => 'Sent when a order status was updated',
            'template'    => 'winter.mall::mail.order.state_changed',
        ]);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::payment.paid',
            'name'        => 'Payment received',
            'description' => 'Sent when a payment has been received',
            'template'    => 'winter.mall::mail.payment.paid',
        ]);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::payment.failed',
            'name'        => 'Payment failed',
            'description' => 'Sent when a payment has failed',
            'template'    => 'winter.mall::mail.payment.failed',
        ]);
        Notification::create([
            'enabled'     => true,
            'code'        => 'winter.mall::payment.refunded',
            'name'        => 'Payment refunded',
            'description' => 'Sent when a payment has been refunded',
            'template'    => 'winter.mall::mail.payment.refunded',
        ]);
        Notification::extend(function () {
            $this->setTable('winter_mall_notifications');
            $this->rules['code'] = str_replace('offline_', 'winter_', $this->rules['code']);
        }, true);
    }
}
