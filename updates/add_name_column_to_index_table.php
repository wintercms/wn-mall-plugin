<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class AddNameColumnToIndexTable extends Migration
{
    public function up()
    {
        if ( ! Schema::hasTable('winter_mall_index')) {
            return;
        }
        Schema::table('winter_mall_index', function (Blueprint $table) {
            if ( ! Schema::hasColumn('winter_mall_index', 'name')) {
                $table->string('name', 191);
            }
        });
    }

    public function down()
    {
        // Do nothing.
    }
}
