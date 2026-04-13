<?php namespace Winter\Mall\Updates;

use Artisan;
use DB;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class RemovePaymentDataColumnFromOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('winter_mall_orders', function ($table) {
            if (Schema::hasColumn('winter_mall_orders', 'payment_data')) {
                $table->dropColumn(['payment_data']);
            }
        });
    }

    public function down()
    {
        Schema::table('winter_mall_orders', function ($table) {
            $table->mediumText('payment_data')->nullable();
        });
    }
}