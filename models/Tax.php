<?php namespace Winter\Mall\Models;

use Illuminate\Support\Facades\Cache;
use Model;
use Winter\Storm\Database\Traits\Validation;
use Winter\Storm\Database\Collection;
use Rainlab\Location\Models\Country as WinterCountry;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Tax extends Model
{
    use Validation;

    public const DEFAULT_TAX_CACHE_KEY = 'mall.taxes.default';

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];
    public $translatable = [
        'name',
    ];
    public $rules = [
        'name'       => 'required',
        'percentage' => 'numeric|min:0|max:100',
    ];
    public $fillable = [
        'name',
        'percentage',
        'is_default',
    ];
    public $table = 'winter_mall_taxes';
    public $casts = ['is_default' => 'boolean'];
    public $belongsToMany = [
        'products'         => [
            Product::class,
            'table'    => 'winter_mall_product_tax',
            'key'      => 'tax_id',
            'otherKey' => 'product_id',
        ],
        'shipping_methods' => [
            ShippingMethod::class,
            'table'    => 'winter_mall_shipping_method_tax',
            'key'      => 'tax_id',
            'otherKey' => 'shipping_method_id',
        ],
        'payment_methods'  => [
            PaymentMethod::class,
            'table'    => 'winter_mall_payment_method_tax',
            'key'      => 'tax_id',
            'otherKey' => 'payment_method_id',
        ],
        'countries'        => [
            WinterCountry::class,
            'table'      => 'winter_mall_country_tax',
            'key'        => 'tax_id',
            'otherKey'   => 'country_id',
            'conditions' => 'is_enabled = true',
        ],
    ];

    public function getPercentageDecimalAttribute()
    {
        return (float)$this->percentage / 100;
    }

    public function afterSave()
    {
        Cache::forget(self::DEFAULT_TAX_CACHE_KEY);
    }

    /**
     * Returns the default taxes.
     *
     * @return Collection<Tax>|Tax[]
     */
    public static function defaultTaxes(): Collection
    {
        $taxes = Cache::rememberForever(static::DEFAULT_TAX_CACHE_KEY, function () {
            $taxes = static::where('is_default', true)->get(['id', 'name', 'percentage', 'is_default']);
            if ( ! $taxes) {
                return [];
            }

            // Make sure the "translations" relation is not cached.
            return $taxes->map->only('id', 'name', 'percentage', 'is_default')->toArray();
        });

        if ( ! $taxes) {
            return new Collection();
        }

        return self::hydrate($taxes);
    }
}
