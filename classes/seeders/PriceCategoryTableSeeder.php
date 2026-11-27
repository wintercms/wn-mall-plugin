<?php

declare(strict_types=1);

namespace Winter\Mall\Classes\Seeders;

use Winter\Mall\Models\PriceCategory;
use Winter\Storm\Database\Updates\Seeder;

class PriceCategoryTableSeeder extends Seeder
{
    public function run()
    {
        $priceCategory = new PriceCategory();
        $priceCategory->code = 'old_price';
        $priceCategory->name = 'Old price';
        $priceCategory->setTable('offline_mall_price_categories');
        $priceCategory->save();

        $priceCategory = new PriceCategory();
        $priceCategory->code = 'msrp';
        $priceCategory->name = 'Manufacturer\'s suggested retail price';
        $priceCategory->setTable('offline_mall_price_categories');
        $priceCategory->save();
    }
}
