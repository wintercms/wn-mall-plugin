<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

class UpdateDescriptionShortColumnOfProductsTable extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_products', function($table)
        {
            $table->text('description_short')->change();
        });
    }

    public function down()
    {
        Schema::table('offline_mall_products', function($table)
        {
            $table->string('description_short', 255)->change();
        });
    }
}