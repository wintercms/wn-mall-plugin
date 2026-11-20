<?php

namespace Winter\Mall\Tests;

use Winter\Mall\Models\Category;
use Winter\Mall\Models\Product;
use Winter\Mall\Models\Property;
use Winter\Mall\Models\PropertyValue;
use Winter\Mall\Models\Variant;

abstract class MallTestCase extends PluginTestCase
{
    protected function createCategory(string $name = 'Test category'): Category
    {
        $category       = new Category();
        $category->name = $name;
        $category->slug = str_slug($name) . '-' . uniqid();
        $category->save();

        return $category->fresh();
    }

    protected function createProduct(array $attributes = []): Product
    {
        $product                              = new Product();
        $product->name                        = $attributes['name'] ?? 'Test product';
        $product->slug                        = $attributes['slug'] ?? 'test-product-' . uniqid();
        $product->meta_description            = 'Test product';
        $product->stock                       = $attributes['stock'] ?? 10;
        $product->price_includes_tax          = true;
        $product->published                   = $attributes['published'] ?? true;
        $product->inventory_management_method = $attributes['inventory_management_method'] ?? 'single';
        $product->save();

        if ( ! empty($attributes['category'])) {
            $product->categories()->attach($attributes['category']->id);
        }

        return $product->fresh();
    }

    protected function createProperty(string $type, array $options = []): Property
    {
        $property          = new Property();
        $property->name    = ucfirst($type) . ' ' . uniqid();
        $property->type    = $type;
        $property->options = $options;
        $property->save();

        return $property->fresh();
    }

    protected function createPropertyValue(
        Property $property,
        $value,
        Product $product,
        ?Variant $variant = null
    ): PropertyValue {
        $propertyValue              = new PropertyValue();
        $propertyValue->property_id = $property->id;
        $propertyValue->product_id  = $product->id;
        $propertyValue->variant_id  = $variant ? $variant->id : null;
        $propertyValue->value       = $value;
        $propertyValue->save();

        return $propertyValue->fresh();
    }

    protected function createVariant(Product $product, array $attributes = []): Variant
    {
        $variant            = new Variant();
        $variant->product_id = $product->id;
        $variant->name       = $attributes['name'] ?? 'Test variant';
        $variant->stock      = $attributes['stock'] ?? 5;
        $variant->published  = $attributes['published'] ?? true;
        $variant->save();

        return $variant->fresh();
    }

    protected function checkboxListOptions(): array
    {
        return [
            ['value' => 'Drinks'],
            ['value' => 'Sauces'],
        ];
    }
}
