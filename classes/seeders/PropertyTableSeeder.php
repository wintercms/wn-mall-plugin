<?php

namespace Winter\Mall\Classes\Seeders;

use Winter\Storm\Database\Updates\Seeder;
use Winter\Mall\Models\Category;
use Winter\Mall\Models\Property;
use Winter\Mall\Models\PropertyGroup;

class PropertyTableSeeder extends Seeder
{
    public function run()
    {
        $method       = new Property();
        $method->name = 'Height';
        $method->type = 'text';
        $method->unit = 'mm';
        $method->setTable('offline_mall_properties');
        $method->save();

        $method       = new Property();
        $method->name = 'Width';
        $method->type = 'text';
        $method->unit = 'mm';
        $method->setTable('offline_mall_properties');
        $method->save();

        $method       = new Property();
        $method->name = 'Depth';
        $method->type = 'text';
        $method->unit = 'mm';
        $method->setTable('offline_mall_properties');
        $method->save();

        $method          = new Property();
        $method->name    = 'Size';
        $method->type    = 'dropdown';
        $method->options = [
            ['value' => 'XS'],
            ['value' => 'S'],
            ['value' => 'M'],
            ['value' => 'L'],
            ['value' => 'XL'],
        ];
        $method->setTable('offline_mall_properties');
        $method->save();

        $propertyGroup = new PropertyGroup();
        $propertyGroup->name = 'Dimensions';
        $propertyGroup->belongsToMany['properties']['table'] = 'offline_mall_property_property_group';
        $propertyGroup->setTable('offline_mall_property_groups');
        $propertyGroup->save();
        $propertyGroup->properties()->attach([1, 2, 3]);

        $propertyGroup = new PropertyGroup();
        $propertyGroup->name = 'Size';
        $propertyGroup->belongsToMany['properties']['table'] = 'offline_mall_property_property_group';
        $propertyGroup->setTable('offline_mall_property_groups');
        $propertyGroup->save();
        $propertyGroup->properties()->attach([4]);

        Category::extend(function () {
            $this->setTable('offline_mall_categories');
            $this->belongsToMany['property_groups']['table'] = 'offline_mall_category_property_group';
        }, true);

        $category = Category::first();
        $category->property_groups()->attach($propertyGroup->id);

        Category::extend(function () {
            $this->setTable('winter_mall_categories');
            $this->belongsToMany['property_groups']['table'] = 'winter_mall_category_property_group';
        }, true);
    }
}
