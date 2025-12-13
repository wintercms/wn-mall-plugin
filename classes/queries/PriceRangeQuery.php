<?php

namespace Winter\Mall\Classes\Queries;

use DB;
use Illuminate\Support\Collection;
use Winter\Storm\Database\QueryBuilder;
use Winter\Mall\Models\Currency;

/**
 * This query fetches the max and min prices of a category.
 */
class PriceRangeQuery
{
    /**
     * The currently active Currency.
     *
     * @var Currency
     */
    protected $currency;
    /**
     * Categories to filter by.
     *
     * @var Collection
     */
    protected $categories;

    public function __construct(Collection $categories, Currency $currency)
    {
        $this->currency   = $currency;
        $this->categories = $categories;
    }

    /**
     * Return the query to filter the max and min price values.
     *
     * @return QueryBuilder
     */
    public function query()
    {
        return DB
            ::table('winter_mall_product_prices')
            ->selectRaw(DB::raw('min(price) as min, max(price) as max'))
            ->join(
                'winter_mall_products',
                'winter_mall_product_prices.product_id',
                '=',
                'winter_mall_products.id'
            )
            ->join(
                'winter_mall_category_product',
                'winter_mall_products.id',
                '=',
                'winter_mall_category_product.product_id'
            )
            ->whereIn('winter_mall_category_product.category_id', $this->categories->pluck('id'))
            ->where('winter_mall_product_prices.currency_id', $this->currency->id);
    }
}
