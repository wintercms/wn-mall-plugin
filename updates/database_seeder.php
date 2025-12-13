<?php namespace Winter\Mall\Updates;

use Cache;
use Winter\Storm\Database\Model;
use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Classes\Registration\BootServiceContainer;
use Winter\Mall\Classes\Registration\BootTwig;
use Winter\Mall\Classes\Seeders\CategoryTableSeeder;
use Winter\Mall\Classes\Seeders\CustomerGroupTableSeeder;
use Winter\Mall\Classes\Seeders\CustomerTableSeeder;
use Winter\Mall\Classes\Seeders\CustomFieldTableSeeder;
use Winter\Mall\Classes\Seeders\NotificationTableSeeder;
use Winter\Mall\Classes\Seeders\OrderStateTableSeeder;
use Winter\Mall\Classes\Seeders\PaymentMethodTableSeeder;
use Winter\Mall\Classes\Seeders\ProductTableSeeder;
use Winter\Mall\Classes\Seeders\PropertyTableSeeder;
use Winter\Mall\Classes\Seeders\ShippingMethodTableSeeder;
use Winter\Mall\Classes\Seeders\TaxTableSeeder;
use Winter\Mall\Models\Currency;
use Winter\Mall\Models\PriceCategory;
use Winter\Mall\Models\ReviewSettings;

class DatabaseSeeder extends Seeder
{
    public $app;

    use BootTwig;
    use BootServiceContainer;

    public function run()
    {
        $this->app = app();

        $this->registerTwigEnvironment();
        $this->registerServices();

        Model::unguard();
        Cache::clear();

        PriceCategory::extend(function () {
            $this->setTable('offline_mall_price_categories');
        }, true);
        PriceCategory::create([
            'code' => 'old_price',
            'name' => 'Old price',
        ]);
        PriceCategory::extend(function () {
            $this->setTable('winter_mall_price_categories');
        }, true);

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

        $this->call(CategoryTableSeeder::class);
        $this->call(TaxTableSeeder::class);
        $this->call(PaymentMethodTableSeeder::class);
        $this->call(ProductTableSeeder::class);
        $this->call(CustomFieldTableSeeder::class);
        $this->call(ShippingMethodTableSeeder::class);
        $this->call(CustomerGroupTableSeeder::class);
        $this->call(CustomerTableSeeder::class);
        $this->call(PropertyTableSeeder::class);
        $this->call(OrderStateTableSeeder::class);
        $this->call(NotificationTableSeeder::class);

        ReviewSettings::set('enabled', true);
    }
}
