<?php namespace Winter\Mall\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class CreateOfflineMallProductCustomField extends Migration
{
    public function up()
    {
        Schema::create('winter_mall_product_custom_field', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('custom_field_id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('winter_mall_product_custom_field');
    }
}
