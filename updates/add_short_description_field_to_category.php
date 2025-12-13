<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

class AddShortDescriptionFieldToCategory extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_categories', function ($table) {
            $table->string('description_short', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::table('offline_mall_categories', function ($table) {
            $table->dropColumn(['description_short']);
        });
    }
}
