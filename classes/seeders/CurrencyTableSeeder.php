<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Mall\Models\Currency;
use Winter\Storm\Database\Updates\Seeder;

class CurrencyTableSeeder extends Seeder
{
    public function run()
    {
        Currency::extend(function () {
            $this->setTable('offline_mall_currencies');
            $this->rules['code'] = str_replace('winter_', 'offline_', $this->rules['code']);
        }, true);

        Currency::create([
            'is_default' => app()->runningUnitTests(),
            'code'       => 'CHF',
            'format'     => '{{ currency.code }} {{ price|number_format(2, ".", "\'") }}',
            'decimals'   => 2,
            'rate'       => 1,
        ]);
        Currency::create([
            'is_default' => ! app()->runningUnitTests(),
            'code'       => 'EUR',
            'format'     => '{{ price|number_format(2, ".", "\'") }}{{ currency.symbol }}',
            'decimals'   => 2,
            'symbol'     => '€',
            'rate'       => 1.14,
        ]);
        Currency::create([
            'is_default' => ! app()->runningUnitTests(),
            'code'       => 'USD',
            'format'     => '{{ currency.symbol }} {{ price|number_format(2, ".", "\'") }}',
            'decimals'   => 2,
            'symbol'     => '$',
            'rate'       => 1.02,
        ]);

        Currency::extend(function () {
            $this->setTable('winter_mall_currencies');
            $this->rules['code'] = str_replace('offline_', 'winter_', $this->rules['code']);
        }, true);
    }
}
