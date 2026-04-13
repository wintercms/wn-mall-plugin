<?php namespace Winter\Mall\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class AddIsDefaultColumnToTaxesTable extends Migration
{
    public function up()
    {
        Schema::table('winter_mall_taxes', function($table)
        {
            $table->boolean('is_default')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('winter_mall_taxes', function($table)
        {
            $table->dropColumn('is_default');
        });
    }
}
