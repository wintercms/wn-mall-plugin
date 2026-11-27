<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Illuminate\Support\Facades\Schema;
use Winter\Mall\Models\Currency;
use Winter\Mall\Models\Product;
use Winter\Mall\Models\ProductPrice;
use Winter\Mall\Models\Variant;

class ProductTableSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment() === 'testing') {
            $legacyTables = Schema::hasTable('offline_mall_products');

            if ($legacyTables) {
                Product::extend(function () {
                    $this->setTable('offline_mall_products');
                }, true);
                ProductPrice::extend(function () {
                    $this->setTable('offline_mall_product_prices');
                }, true);
                Variant::extend(function () {
                    $this->setTable('offline_mall_product_variants');
                }, true);
                Currency::extend(function () {
                    $this->setTable('offline_mall_currencies');
                }, true);
            }

            try {
                $product       = new Product();
                $product->name = 'Test';

                $product->slug      = 'test';
                $product->stock     = 20;
                $product->published = true;
                $product->save();
                $product->price = ['CHF' => 20, 'EUR' => 30];

                $product       = new Product();
                $product->name = 'Test 2';

                $product->slug      = 'test-2';
                $product->stock     = 90;
                $product->published = true;
                $product->save();
                $product->price = ['CHF' => 30, 'EUR' => 40];
            } finally {
                if ($legacyTables) {
                    Product::extend(function () {
                        $this->setTable('winter_mall_products');
                    }, true);
                    ProductPrice::extend(function () {
                        $this->setTable('winter_mall_product_prices');
                    }, true);
                    Variant::extend(function () {
                        $this->setTable('winter_mall_product_variants');
                    }, true);
                    Currency::extend(function () {
                        $this->setTable('winter_mall_currencies');
                    }, true);
                }
            }
        }
    }
}
