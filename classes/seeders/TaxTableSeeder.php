<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Models\Tax;

class TaxTableSeeder extends Seeder
{
    public function run()
    {
        Tax::extend(function () {
            $this->setTable('offline_mall_taxes');
        }, true);

        $method             = new Tax();
        $method->name       = 'Default';
        $method->percentage = 8;
        $method->save();

        Tax::extend(function () {
            $this->setTable('winter_mall_taxes');
        }, true);
    }
}
