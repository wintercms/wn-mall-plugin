<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Models\CustomerGroup;

class CustomerGroupTableSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment() === 'testing') {
            $group       = new CustomerGroup();
            $group->name = 'Gold Partners';
            $group->code = 'gold';
            $group->save();

            $group       = new CustomerGroup();
            $group->name = 'Silver Partners';
            $group->code = 'silver';
            $group->save();
        }
    }
}
