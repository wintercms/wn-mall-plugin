<?php namespace Winter\Mall\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class AddRoundingColumnToCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_currencies', function($table)
        {
            $table->integer('rounding')->nullable();
        });
    }

    public function down()
    {
        Schema::table('offline_mall_currencies', function($table)
        {
            $table->dropColumn('rounding');
        });
    }
}

