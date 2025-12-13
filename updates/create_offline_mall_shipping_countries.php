<?php namespace Winter\Mall\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class CreateOfflineMallShippingCountries extends Migration
{
    public function up()
    {
        Schema::create('offline_mall_shipping_countries', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('shipping_method_id')->unsigned();
            $table->integer('country_id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('offline_mall_shipping_countries');
    }
}
