<?php namespace Winter\Mall\Models;

use Winter\Storm\Database\Traits\Nullable;
use Winter\Storm\Database\Traits\Validation;

class ProductPrice extends Price
{
    use Validation;
    use Nullable;

    public $table = 'winter_mall_product_prices';
    public $nullable = ['price', 'variant_id'];
    protected $touches = ['product', 'variant'];
    // Remove parent relation
    public $morphTo = [
    ];
    public $fillable = [
        'price',
        'currency_id',
        'customer_group_id',
        'product_id',
        'variant_id',
    ];
    public $belongsTo = [
        'product'  => Product::class,
        'variant'  => Variant::class,
        'currency' => Currency::class,
    ];
}
