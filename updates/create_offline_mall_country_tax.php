<?php namespace Winter\Mall\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class CreateOfflineMallCountryTax extends Migration
{
    public function up()
    {
        Schema::create('offline_mall_country_tax', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('country_id');
            $table->integer('tax_id');

            $table->unique(['country_id', 'tax_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('offline_mall_country_tax');
    }
}
