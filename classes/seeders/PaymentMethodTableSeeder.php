<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Models\PaymentMethod;

class PaymentMethodTableSeeder extends Seeder
{
    public function run()
    {
        $method                   = new PaymentMethod();
        $method->name             = 'Stripe';
        $method->payment_provider = 'stripe';
        $method->sort_order       = 1;
        $method->setTable('offline_mall_payment_methods');
        $method->save();
        
        $method                   = new PaymentMethod();
        $method->name             = 'PayPal';
        $method->payment_provider = 'paypal-rest';
        $method->sort_order       = 2;
        $method->setTable('offline_mall_payment_methods');
        $method->save();

        $method                   = new PaymentMethod();
        $method->name             = 'Invoice';
        $method->payment_provider = 'offline';
        $method->sort_order       = 3;
        $method->setTable('offline_mall_payment_methods');
        $method->save();
    }
}
