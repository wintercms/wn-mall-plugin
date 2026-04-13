<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

class AddWidthAndLengthToProductModel extends Migration
{
    public function up()
    {
        Schema::table('winter_mall_products', function ($table) {
            $table->integer('length')->after('weight')->unsigned()->nullable();
            $table->integer('width')->after('length')->unsigned()->nullable();
            $table->integer('height')->after('width')->unsigned()->nullable();
        });
        Schema::table('winter_mall_product_variants', function ($table) {
            $table->integer('length')->after('weight')->unsigned()->nullable();
            $table->integer('width')->after('length')->unsigned()->nullable();
            $table->integer('height')->after('width')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::table('winter_mall_products', function ($table) {
            $table->dropColumn(['length', 'width', 'height']);
        });
        Schema::table('winter_mall_product_variants', function ($table) {
            $table->dropColumn(['length', 'width', 'height']);
        });
    }
}
