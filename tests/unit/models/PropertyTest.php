<?php

namespace Winter\Mall\Tests\Unit\Models;

use Winter\Mall\Models\Property;
use Winter\Mall\Tests\MallTestCase;

class PropertyTest extends MallTestCase
{
    public function test_checkboxlist_values_are_split_into_individual_filter_options()
    {
        $category = $this->createCategory();
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());

        $product = $this->createProduct(['category' => $category]);
        $this->createPropertyValue($property, ['Drinks', 'Sauces'], $product);

        $otherProduct = $this->createProduct(['category' => $category]);
        $this->createPropertyValue($property, ['Drinks'], $otherProduct);

        $values = Property::getValuesForCategory(collect([$category]))->get($property->id);

        $this->assertSame(['Drinks', 'Sauces'], $values->pluck('value')->all());
        $this->assertSame(['drinks', 'sauces'], $values->pluck('index_value')->all());
        $this->assertSame(['Drinks', 'Sauces'], $values->pluck('display_value')->all());
    }

    public function test_checkboxlist_filter_options_follow_the_property_option_order()
    {
        $category = $this->createCategory();
        $property = $this->createProperty('checkboxlist', [
            ['value' => 'Sauces'],
            ['value' => 'Drinks'],
        ]);

        $product = $this->createProduct(['category' => $category]);
        $this->createPropertyValue($property, ['Drinks', 'Sauces'], $product);

        $values = Property::getValuesForCategory(collect([$category]))->get($property->id);

        $this->assertSame(['Sauces', 'Drinks'], $values->pluck('value')->all());
    }

    public function test_text_value_that_looks_like_json_is_not_split_into_filter_options()
    {
        $category = $this->createCategory();
        $property = $this->createProperty('text');

        $product = $this->createProduct(['category' => $category]);
        $this->createPropertyValue($property, '["Drinks","Sauces"]', $product);

        $values = Property::getValuesForCategory(collect([$category]))->get($property->id);

        $this->assertCount(1, $values);
        $this->assertSame('["Drinks","Sauces"]', $values->first()->value);
        $this->assertSame('drinkssauces', $values->first()->index_value);
    }

    public function test_checkboxlist_filter_options_use_the_translated_label_of_each_option()
    {
        $category = $this->createCategory();
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());

        $product = $this->createProduct(['category' => $category]);
        $value   = $this->createPropertyValue($property, ['Drinks', 'Sauces'], $product);

        $value->setAttributeTranslated('value', json_encode(['Getränke', 'Soßen']), 'de');
        $value->save();

        $values = Property::getValuesForCategory(collect([$category]))->get($property->id);
        $labels = $values->keyBy('value');

        $this->assertSame('Getränke', $labels['Drinks']->getAttributeTranslated('value', 'de'));
        $this->assertSame('Soßen', $labels['Sauces']->getAttributeTranslated('value', 'de'));
    }

    public function test_filter_options_from_different_arrays_are_not_duplicated()
    {
        $category = $this->createCategory();
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());

        $first = $this->createProduct(['category' => $category]);
        $this->createPropertyValue($property, ['Drinks', 'Sauces'], $first);

        $second = $this->createProduct(['category' => $category]);
        $this->createPropertyValue($property, ['Sauces'], $second);

        $values = Property::getValuesForCategory(collect([$category]))->get($property->id);

        $this->assertSame(['Drinks', 'Sauces'], $values->pluck('value')->all());
    }
}
