<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class AddGoogleCategoryIdToCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_categories', function (Blueprint $table) {
            $table->integer('google_product_category_id')->after('sort_order')->nullable();
        });
    }

    public function down()
    {
        Schema::table('offline_mall_categories', function (Blueprint $table) {
            $table->dropColumn(['google_product_category_id']);
        });
    }
}
