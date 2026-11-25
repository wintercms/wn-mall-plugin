<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Illuminate\Support\Facades\Schema;
use Winter\Mall\Models\CustomField;
use Winter\Mall\Models\CustomFieldOption;
use Winter\Mall\Models\Product;

class CustomFieldTableSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment() === 'testing') {
            $legacyTables = Schema::hasTable('offline_mall_custom_fields');

            if ($legacyTables) {
                CustomField::extend(function () {
                    $this->setTable('offline_mall_custom_fields');
                    $this->belongsToMany['products']['table'] = 'offline_mall_product_custom_field';
                }, true);
                CustomFieldOption::extend(function () {
                    $this->setTable('offline_mall_custom_field_options');
                }, true);
                Product::extend(function () {
                    $this->setTable('offline_mall_products');
                }, true);
            }

            try {
                $field       = new CustomField();
                $field->name = 'Test';
                $field->type = 'dropdown';
                $field->save();

                $option               = new CustomFieldOption();
                $option->name         = 'Test';
                $option->option_value = 'test';
                $option->sort_order   = 1;
                $field->custom_field_options()->save($option);

                $option               = new CustomFieldOption();
                $option->name         = 'Test2';
                $option->option_value = 'test2';
                $option->sort_order   = 2;
                $field->custom_field_options()->save($option);
                $field->products()->attach(Product::first()->id);
            } finally {
                if ($legacyTables) {
                    CustomField::extend(function () {
                        $this->setTable('winter_mall_custom_fields');
                        $this->belongsToMany['products']['table'] = 'winter_mall_product_custom_field';
                    }, true);
                    CustomFieldOption::extend(function () {
                        $this->setTable('winter_mall_custom_field_options');
                    }, true);
                    Product::extend(function () {
                        $this->setTable('winter_mall_products');
                    }, true);
                }
            }
        }
    }
}
