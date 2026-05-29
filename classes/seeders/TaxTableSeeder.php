<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Models\Tax;

class TaxTableSeeder extends Seeder
{
    public function run()
    {
        $method             = new Tax();
        $method->name       = 'Default';
        $method->percentage = 8;
        $method->setTable('offline_mall_taxes');
        $method->save();
    }
}
