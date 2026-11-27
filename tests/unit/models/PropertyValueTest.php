<?php

namespace Winter\Mall\Tests\Unit\Models;

use Winter\Mall\Models\PropertyValue;
use Winter\Mall\Tests\MallTestCase;

class PropertyValueTest extends MallTestCase
{
    public function test_text_and_textarea_values_round_trip()
    {
        foreach (['text', 'textarea'] as $type) {
            $property = $this->createProperty($type);
            $product  = $this->createProduct();
            $value    = $this->createPropertyValue($property, 'Hello world', $product);

            $this->assertSame('Hello world', $value->value);
            $this->assertSame('Hello world', $value->display_value);
            $this->assertSame('hello-world', $value->index_value);
        }
    }

    public function test_dropdown_value_round_trip()
    {
        $property = $this->createProperty('dropdown', [
            ['value' => 'Red'],
            ['value' => 'Blue'],
        ]);
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, 'Red', $product);

        $this->assertSame('Red', $value->value);
        $this->assertSame('Red', $value->display_value);
        $this->assertSame('red', $value->index_value);
    }

    public function test_checkbox_value_round_trip()
    {
        $property = $this->createProperty('checkbox');
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, '1', $product);

        $this->assertTrue($value->value);
        $this->assertSame(trans('winter.mall::lang.common.yes'), $value->display_value);
        $this->assertSame('1', $value->index_value);
    }

    public function test_float_value_round_trip()
    {
        $property = $this->createProperty('float');
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, '12.5', $product);

        $this->assertSame(12.5, $value->value);
        $this->assertSame('12.5', $value->display_value);
    }

    public function test_integer_value_round_trip()
    {
        $property = $this->createProperty('integer');
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, '42', $product);

        $this->assertSame(42, $value->value);
        $this->assertSame('42', $value->display_value);
        $this->assertSame('42', $value->index_value);
    }

    public function test_color_value_round_trip()
    {
        $property = $this->createProperty('color');
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, ['name' => 'Red', 'hex' => '#ff0000'], $product);

        $this->assertSame(['name' => 'Red', 'hex' => '#ff0000'], $value->value);
        $this->assertSame('red', $value->index_value);
        $this->assertStringContainsString('background: #ff0000', $value->display_value);
        $this->assertStringContainsString('title="Red"', $value->display_value);
    }

    public function test_checkboxlist_value_is_decoded_into_an_array()
    {
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, ['Drinks', 'Sauces'], $product);

        $this->assertSame(['Drinks', 'Sauces'], $value->value);
        $this->assertSame('Drinks, Sauces', $value->display_value);
        // index_value keeps the legacy slug of the whole stored value.
        $this->assertSame('drinkssauces', $value->index_value);
    }

    public function test_checkboxlist_single_value_is_decoded_into_an_array()
    {
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, ['Drinks'], $product);

        $this->assertSame(['Drinks'], $value->value);
        $this->assertSame('Drinks', $value->display_value);
    }

    public function test_checkboxlist_preserves_the_zero_option()
    {
        $property = $this->createProperty('checkboxlist', [
            ['value' => '0'],
            ['value' => 'Drinks'],
        ]);
        $product = $this->createProduct();
        $value   = $this->createPropertyValue($property, ['0', 'Drinks'], $product);

        $this->assertSame(['0', 'Drinks'], $value->value);
        $this->assertSame('0, Drinks', $value->display_value);
    }

    public function test_null_and_empty_values_are_not_wrapped_in_arrays()
    {
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());
        $product  = $this->createProduct();

        $nullValue = $this->createPropertyValue($property, null, $product);
        $this->assertNull(PropertyValue::find($nullValue->id)->value);

        $emptyValue = $this->createPropertyValue($property, '', $product);
        $this->assertSame('', PropertyValue::find($emptyValue->id)->value);
    }

    public function test_text_value_that_looks_like_json_is_not_decoded()
    {
        $property = $this->createProperty('text');
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, '["Drinks","Sauces"]', $product);

        $this->assertSame('["Drinks","Sauces"]', $value->value);
        $this->assertSame(e('["Drinks","Sauces"]'), $value->display_value);
    }

    public function test_checkboxlist_splits_into_single_values()
    {
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, ['Drinks', 'Sauces'], $product);

        $split = $value->splitIntoSingleValues();

        $this->assertSame(['Drinks', 'Sauces'], $split->pluck('value')->all());
        $this->assertSame(['drinks', 'sauces'], $split->pluck('index_value')->all());
        $this->assertSame($property->id, $split->first()->property->id);
    }

    public function test_checkboxlist_split_keeps_translated_labels_per_option()
    {
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, ['Drinks', 'Sauces'], $product);

        $value->setAttributeTranslated('value', json_encode(['Getränke', 'Soßen']), 'de');
        $value->save();

        $value = PropertyValue::with('translations')->find($value->id);
        $split = $value->splitIntoSingleValues();

        $this->assertSame('Getränke', $split->first()->getAttributeTranslated('value', 'de'));
        $this->assertSame('Soßen', $split->last()->getAttributeTranslated('value', 'de'));
    }

    public function test_empty_checkboxlist_is_not_part_of_the_properties_description()
    {
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());
        $product  = $this->createProduct();
        $this->createPropertyValue($property, [], $product);

        $product = $product->fresh();
        $this->assertSame('', $product->propertyValuesAsString());
    }
}
