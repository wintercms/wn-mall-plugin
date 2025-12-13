<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class AddValidFromColumnToDiscountsTable extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_discounts', function (Blueprint $table) {
            $table->dateTime('valid_from')->after('max_number_of_usages')->nullable();
        });
    }

    public function down()
    {
        Schema::table('offline_mall_discounts', function (Blueprint $table) {
            $table->dropColumn(['valid_from']);
        });
    }
}
