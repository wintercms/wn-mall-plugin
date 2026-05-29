<?php

namespace Winter\Mall\Classes\Queries;

use DB;
use Illuminate\Support\Collection;
use Winter\Storm\Database\QueryBuilder;

/**
 * This query is used to get a list of all unique property values in one or
 * more categories. It is used to display a set of possible filters
 * for all available property values.
 */
class UniquePropertyValuesInCategoriesQuery
{
    /**
     * An array of category ids.
     * @var Collection
     */
    protected $categories;

    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Return a query to get all unique product property values.
     *
     * @return QueryBuilder
     */
    public function query()
    {
        return DB
            ::table('winter_mall_products')
            ->selectRaw('
                MIN(winter_mall_property_values.id) AS id,
                winter_mall_property_values.value,
                winter_mall_property_values.index_value,
                winter_mall_property_values.property_id'
            )
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('winter_mall_products.published', true)
                      ->whereNull('winter_mall_product_variants.id');
                })->orWhere('winter_mall_product_variants.published', true);
            })
            ->whereIn('winter_mall_category_product.category_id', $this->categories->pluck('id'))
            ->whereNull('winter_mall_product_variants.deleted_at')
            ->whereNull('winter_mall_products.deleted_at')
            ->where('winter_mall_property_values.value', '<>', '')
            ->whereNotNull('winter_mall_property_values.value')
            ->groupBy(
                'winter_mall_property_values.value',
                'winter_mall_property_values.index_value',
                'winter_mall_property_values.property_id'
            )
            ->leftJoin(
                'winter_mall_product_variants',
                'winter_mall_products.id',
                '=',
                'winter_mall_product_variants.product_id'
            )
            ->leftJoin(
                'winter_mall_category_product',
                'winter_mall_products.id',
                '=',
                'winter_mall_category_product.product_id'
            )
            ->join(
                'winter_mall_property_values',
                'winter_mall_products.id',
                '=',
                'winter_mall_property_values.product_id'
            );
    }

    /**
     * Get all unique property values, properly handling checkboxlist JSON arrays.
     *
     * @return Collection
     */
    public function get()
    {
        $raw = $this->query()->get();

        // Process the results to expand checkboxlist JSON values into individual entries
        $processed = collect();
        foreach ($raw as $row) {
            $propertyId = $row->property_id;
            $value = $row->value;
            $indexValue = $row->index_value;

            // Check if this value is a JSON array (checkboxlist with multiple values)
            $decoded = json_decode($value, true);
            if (is_array($decoded) && !$this->isAssoc($decoded)) {
                // This is a checkboxlist JSON array - create individual entries for each value
                foreach ($decoded as $singleValue) {
                    $sluggedValue = str_slug($singleValue);
                    // Avoid duplicates
                    $key = $propertyId . '_' . $sluggedValue;
                    if (!$processed->has($key)) {
                        $processed->put($key, (object)[
                            'id' => $row->id,
                            'value' => $singleValue,
                            'index_value' => $sluggedValue,
                            'property_id' => $propertyId,
                        ]);
                    }
                }
            } else {
                // Regular value - keep as is
                $key = $propertyId . '_' . $indexValue;
                if (!$processed->has($key)) {
                    $processed->put($key, $row);
                }
            }
        }

        return $processed->values();
    }

    /**
     * Check if an array is associative (not a sequential list).
     *
     * @param array $arr
     * @return bool
     */
    private function isAssoc(array $arr)
    {
        if (empty($arr)) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
