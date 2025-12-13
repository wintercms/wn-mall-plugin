<?php namespace Winter\Mall\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class AddEmbedsColumnToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_products', function($table)
        {
            $table->text('embeds')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('offline_mall_products', function($table)
        {
            $table->dropColumn('embeds');
        });
    }
}
