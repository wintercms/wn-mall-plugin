<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateOfflineMallCategoryProductSortOrder extends Migration
{
    public function up()
    {
        Schema::create('winter_mall_category_product_sort_order', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('sort_order')->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('winter_mall_category_product_sort_order');
    }
}
