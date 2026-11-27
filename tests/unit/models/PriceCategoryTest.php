<?php

declare(strict_types=1);

namespace Winter\Mall\Tests\Unit\Models;

use Illuminate\Support\Facades\DB;
use Winter\Mall\Models\Currency;
use Winter\Mall\Models\Price;
use Winter\Mall\Models\PriceCategory;
use Winter\Mall\Models\Product;
use Winter\Mall\Tests\PluginTestCase;

class PriceCategoryTest extends PluginTestCase
{
    /**
     * Create and receive additional prices.
     * 
     * @return void
     */
    public function test_create_additional_prices()
    {
        $this->prepare();

        // Fetch prices, added by the prepare method.
        $product = Product::first();
        $prices = $product->additional_prices()->get()->mapWithKeys(
            fn ($price) => [$price->category->code => $price->float]
        )->toArray();

        // Check if values has been added correctly.
        $this->assertEquals([
            'old_price' => 50.0,
            'msrp' => 60.0,
        ], $prices);
    }

    /**
     * Delete prices from each price category.
     * 
     * @return void
     */
    public function test_delete_all_prices()
    {
        $this->prepare();

        // Delete Prices, even from disabled price categories
        $product = Product::first();
        $product->additional_prices()->delete();

        // Check if prices has been deleted
        $prices = DB::table('winter_mall_prices')
            ->where('priceable_type', Product::MORPH_KEY)
            ->where('priceable_id', $product->id)
            ->count();
        $this->assertEquals($prices, 0);
    }

    /**
     * Prepare Tests below
     * 
     * @return void
     */
    protected function prepare()
    {
        $product = Product::first();
        $currency = Currency::where('code', 'CHF')->first();
        $oldPriceCategory = PriceCategory::where('code', 'old_price')->first();
        $msrpPriceCategory = PriceCategory::where('code', 'msrp')->first();

        $product->additional_prices()->save(new Price([
            'price'             => 50,
            'price_category_id' => $oldPriceCategory->id,
            'currency_id'       => $currency->id,
        ]));
        $product->additional_prices()->save(new Price([
            'price'             => 60,
            'price_category_id' => $msrpPriceCategory->id,
            'currency_id'       => $currency->id,
        ]));
    }
}