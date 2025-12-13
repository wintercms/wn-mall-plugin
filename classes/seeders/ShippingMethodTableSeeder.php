<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Models\Price;
use Winter\Mall\Models\ShippingMethod;

class ShippingMethodTableSeeder extends Seeder
{
    public function run()
    {
        ShippingMethod::extend(function () {
            $this->setTable('offline_mall_shipping_methods');
        }, true);

        $method             = new ShippingMethod();
        $method->name       = 'Default';
        $method->sort_order = 1;
        $method->save();

        Price::extend(function () {
            $this->setTable('offline_mall_prices');
        }, true);

        (new Price([
            'price'          => 10,
            'currency_id'    => 1,
            'priceable_type' => ShippingMethod::MORPH_KEY,
            'priceable_id'   => $method->id,
        ]))->save();

        (new Price([
            'price'          => 12,
            'currency_id'    => 2,
            'priceable_type' => ShippingMethod::MORPH_KEY,
            'priceable_id'   => $method->id,
        ]))->save();

        (new Price([
            'price'          => 15,
            'currency_id'    => 3,
            'priceable_type' => ShippingMethod::MORPH_KEY,
            'priceable_id'   => $method->id,
        ]))->save();

        Price::extend(function () {
            $this->setTable('winter_mall_prices');
        }, true);

        ShippingMethod::extend(function () {
            $this->setTable('winter_mall_shipping_methods');
        }, true);
    }
}
