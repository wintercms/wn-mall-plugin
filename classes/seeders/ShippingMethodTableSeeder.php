<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Models\Price;
use Winter\Mall\Models\ShippingMethod;

class ShippingMethodTableSeeder extends Seeder
{
    public function run()
    {
        $method             = new ShippingMethod();
        $method->setTable('winter_mall_shipping_methods');
        $method->name       = 'Default';
        $method->sort_order = 1;
        $method->save();

        (new Price([
            'price'          => 10,
            'currency_id'    => 1,
            'priceable_type' => ShippingMethod::MORPH_KEY,
            'priceable_id'   => $method->id,
        ]))->setTable('winter_mall_prices')->save();

        (new Price([
            'price'          => 12,
            'currency_id'    => 2,
            'priceable_type' => ShippingMethod::MORPH_KEY,
            'priceable_id'   => $method->id,
        ]))->setTable('winter_mall_prices')->save();

        (new Price([
            'price'          => 15,
            'currency_id'    => 3,
            'priceable_type' => ShippingMethod::MORPH_KEY,
            'priceable_id'   => $method->id,
        ]))->setTable('winter_mall_prices')->save();
    }
}
