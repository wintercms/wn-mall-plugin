<?php namespace Winter\Mall\Updates;

use Artisan;
use DB;
use Winter\Storm\Database\Updates\Migration;
use Schema;


class MigrateCategoriesToBelongstoManyRelation extends Migration
{
    public function up()
    {
        Schema::create('winter_mall_category_product', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('sort_order')->unsigned()->nullable();
        });

        // Migrate products to new structure. Migrate the category sort order as well.
        $sortOrders = DB::table('winter_mall_category_product_sort_order')->get()->mapWithKeys(function ($item) {
            return [$item->category_id . '-' . $item->product_id => $item->sort_order];
        });

        $products = \DB::table('winter_mall_products')->get();
        $products->each(function ($product, $index) use ($sortOrders) {
            if ($product->category_id === null) {
                return;
            }

            $orderKey  = $product->category_id . '-' . $product->id;
            $sortOrder = $sortOrders[$orderKey] ?? $index;

            DB::table('winter_mall_category_product')->insert([
                'product_id'  => $product->id,
                'category_id' => $product->category_id,
                'sort_order'  => $sortOrder,
            ]);
        });

        Schema::table('winter_mall_products', function ($table) {
            $table->dropColumn(['category_id']);
        });

        Schema::drop('winter_mall_category_product_sort_order');
    }

    public function down()
    {
        Schema::dropIfExists('winter_mall_category_product');
        Schema::table('winter_mall_products', function ($table) {
            $table->integer('category_id')->nullable();
        });
    }
}
