<?php

namespace Winter\Mall\Classes\Registration;

use Winter\Storm\Database\Relations\Relation;
use Winter\Mall\Models\Category;
use Winter\Mall\Models\CustomField;
use Winter\Mall\Models\CustomFieldOption;
use Winter\Mall\Models\Discount;
use Winter\Mall\Models\ImageSet;
use Winter\Mall\Models\PaymentMethod;
use Winter\Mall\Models\Product;
use Winter\Mall\Models\ServiceOption;
use Winter\Mall\Models\ShippingMethod;
use Winter\Mall\Models\ShippingMethodRate;
use Winter\Mall\Models\Variant;

trait BootRelations
{

    public function registerRelations()
    {
        Relation::morphMap([
            Category::MORPH_KEY           => Category::class,
            Variant::MORPH_KEY            => Variant::class,
            Product::MORPH_KEY            => Product::class,
            ImageSet::MORPH_KEY           => ImageSet::class,
            Discount::MORPH_KEY           => Discount::class,
            CustomField::MORPH_KEY        => CustomField::class,
            PaymentMethod::MORPH_KEY      => PaymentMethod::class,
            ShippingMethod::MORPH_KEY     => ShippingMethod::class,
            CustomFieldOption::MORPH_KEY  => CustomFieldOption::class,
            ShippingMethodRate::MORPH_KEY => ShippingMethodRate::class,
            ServiceOption::MORPH_KEY      => ServiceOption::class,
        ]);
    }
}
