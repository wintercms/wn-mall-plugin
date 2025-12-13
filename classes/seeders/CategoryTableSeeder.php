<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Models\Category;

class CategoryTableSeeder extends Seeder
{
    public function run()
    {
        Category::extend(function () {
            $this->setTable('offline_mall_categories');
        }, true);

        $category       = new Category();
        $category->name = 'Example category';
        $category->slug = 'example';
        $category->save();

        Category::extend(function () {
            $this->setTable('winter_mall_categories');
        }, true);
    }
}
