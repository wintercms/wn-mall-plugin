<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class AddBrandColumnToOrderProductsTable extends Migration
{
    public function up()
    {
        Schema::table('winter_mall_order_products', function (Blueprint $table) {
            $table->text('brand')->after('variant_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('winter_mall_order_products', function (Blueprint $table) {
            $table->dropColumn(['brand']);
        });
    }
}
