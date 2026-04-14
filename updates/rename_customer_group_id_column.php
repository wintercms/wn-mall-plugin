<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Winter\Storm\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'offline_mall_customer_group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('offline_mall_customer_group_id', 'winter_mall_customer_group_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'winter_mall_customer_group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('winter_mall_customer_group_id', 'offline_mall_customer_group_id');
            });
        }
    }
};
