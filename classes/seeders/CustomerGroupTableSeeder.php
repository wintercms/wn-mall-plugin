<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Illuminate\Support\Facades\Schema;
use Winter\Mall\Models\CustomerGroup;

class CustomerGroupTableSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment() === 'testing') {
            $legacyTables = Schema::hasTable('offline_mall_customer_groups');

            if ($legacyTables) {
                CustomerGroup::extend(function () {
                    $this->setTable('offline_mall_customer_groups');
                }, true);
            }

            try {
                $group       = new CustomerGroup();
                $group->name = 'Gold Partners';
                $group->code = 'gold';
                $group->save();

                $group       = new CustomerGroup();
                $group->name = 'Silver Partners';
                $group->code = 'silver';
                $group->save();
            } finally {
                if ($legacyTables) {
                    CustomerGroup::extend(function () {
                        $this->setTable('winter_mall_customer_groups');
                    }, true);
                }
            }
        }
    }
}
