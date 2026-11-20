<?php

namespace Winter\Mall\Tests\Classes\Index;

use Winter\Mall\Classes\Index\ProductEntry;
use Winter\Mall\Classes\Index\VariantEntry;
use Winter\Mall\Models\PropertyValue;
use Winter\Mall\Tests\MallTestCase;

class ProductEntryTest extends MallTestCase
{
    public function test_checkboxlist_values_become_individual_index_tokens()
    {
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());
        $product  = $this->createProduct();
        $this->createPropertyValue($property, ['Drinks', 'Sauces'], $product);

        $tokens = (new ProductEntry($product->fresh()))->data()['property_values'][$property->id];

        $this->assertSame(['drinks', 'sauces'], $tokens->all());
    }

    public function test_checkboxlist_tokens_are_derived_from_the_stored_value_on_reindex()
    {
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());
        $product  = $this->createProduct();
        $value    = $this->createPropertyValue($property, ['Drinks', 'Sauces'], $product);

        // Simulate a record written before this fix, which kept the whole array slug.
        PropertyValue::where('id', $value->id)->update(['index_value' => 'drinks-sauces']);

        $tokens = (new ProductEntry($product->fresh()))->data()['property_values'][$property->id];

        $this->assertSame(['drinks', 'sauces'], $tokens->all());
    }

    public function test_the_zero_option_is_kept_as_an_index_token()
    {
        $property = $this->createProperty('checkboxlist', [
            ['value' => '0'],
            ['value' => 'Drinks'],
        ]);
        $product = $this->createProduct();
        $this->createPropertyValue($property, ['0', 'Drinks'], $product);

        $tokens = (new ProductEntry($product->fresh()))->data()['property_values'][$property->id];

        $this->assertSame(['0', 'drinks'], $tokens->all());
    }

    public function test_text_values_are_not_split_into_multiple_tokens()
    {
        $property = $this->createProperty('text');
        $product  = $this->createProduct();
        $this->createPropertyValue($property, 'Drinks.Sauces', $product);

        $tokens = (new ProductEntry($product->fresh()))->data()['property_values'][$property->id];

        $this->assertSame(['drinkssauces'], $tokens->all());
    }

    public function test_variant_entry_splits_checkboxlist_values()
    {
        $property = $this->createProperty('checkboxlist', $this->checkboxListOptions());
        $product  = $this->createProduct();
        $variant  = $this->createVariant($product);
        $this->createPropertyValue($property, ['Drinks', 'Sauces'], $product, $variant);

        $tokens = (new VariantEntry($variant->fresh()))->data()['property_values'][$property->id];

        $this->assertSame(['drinks', 'sauces'], $tokens->all());
    }
}
