<?php namespace Winter\Mall\Updates;

use Artisan;
use DB;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class UseTextColumnsForVariantNames extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_order_products', function ($table) {
            $table->text('variant_name')->change();
        });
    }

    public function down()
    {
        // Leave the columns. The migration might fail if data gets truncated.
    }
}