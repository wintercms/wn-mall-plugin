<?php namespace Winter\Mall\Updates;

use Artisan;
use DB;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class RemoveDescriptionColumnFromOrderProducts extends Migration
{
    public function up()
    {
        Schema::table('winter_mall_order_products', function ($table) {
            if (Schema::hasColumn('winter_mall_order_products', 'description')) {
                $table->dropColumn(['description']);
            }
        });
    }

    public function down()
    {
        Schema::table('winter_mall_order_products', function ($table) {
            $table->longText('description')->nullable();
        });
    }
}