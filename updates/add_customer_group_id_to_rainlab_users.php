<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Schema;
use Winter\Storm\Database\Updates\Migration;

class AddCustomerGroupIdToRainlabUsers extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('users', 'offline_mall_customer_group_id')) {
            return;
        }
        Schema::table('users', function (Blueprint $table) {
            $table->integer('offline_mall_customer_group_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'offline_mall_customer_group_id')) {
                $table->dropColumn(['offline_mall_customer_group_id']);
            }
        });
    }
}
