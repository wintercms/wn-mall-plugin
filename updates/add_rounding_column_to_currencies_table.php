<?php namespace Winter\Mall\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class AddRoundingColumnToCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::table('winter_mall_currencies', function($table)
        {
            $table->integer('rounding')->nullable();
        });
    }

    public function down()
    {
        Schema::table('winter_mall_currencies', function($table)
        {
            $table->dropColumn('rounding');
        });
    }
}

