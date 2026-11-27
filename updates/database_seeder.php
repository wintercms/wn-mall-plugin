<?php namespace Winter\Mall\Updates;

use Cache;
use Winter\Mall\Classes\Registration\BootServiceContainer;
use Winter\Mall\Classes\Registration\BootTwig;
use Winter\Mall\Classes\Seeders\CategoryTableSeeder;
use Winter\Mall\Classes\Seeders\CustomerGroupTableSeeder;
use Winter\Mall\Classes\Seeders\CustomerTableSeeder;
use Winter\Mall\Classes\Seeders\CurrencyTableSeeder;
use Winter\Mall\Classes\Seeders\CustomFieldTableSeeder;
use Winter\Mall\Classes\Seeders\NotificationTableSeeder;
use Winter\Mall\Classes\Seeders\OrderStateTableSeeder;
use Winter\Mall\Classes\Seeders\PaymentMethodTableSeeder;
use Winter\Mall\Classes\Seeders\PriceCategoryTableSeeder;
use Winter\Mall\Classes\Seeders\ProductTableSeeder;
use Winter\Mall\Classes\Seeders\PropertyTableSeeder;
use Winter\Mall\Classes\Seeders\ShippingMethodTableSeeder;
use Winter\Mall\Classes\Seeders\TaxTableSeeder;
use Winter\Mall\Models\PriceCategory;
use Winter\Mall\Models\ReviewSettings;
use Winter\Storm\Database\Model;
use Winter\Storm\Database\Updates\Seeder;

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

        $this->call(PriceCategoryTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
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

        Model::reguard();
        Cache::clear();
    }
}
