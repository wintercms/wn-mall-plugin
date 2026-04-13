<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateOfflineMallProductAccessory extends Migration
{
    public function up()
    {
        Schema::create('winter_mall_product_accessory', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('accessory_id')->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('winter_mall_product_accessory');
    }
}
