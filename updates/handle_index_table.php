<?php namespace Winter\Mall\Updates;

use Artisan;
use DB;
use Winter\Storm\Database\Updates\Migration;
use Schema;


class HandleIndexTable extends Migration
{
    public function up()
    {
    }

    public function down()
    {
        Schema::dropIfExists('offline_mall_index');
    }
}
