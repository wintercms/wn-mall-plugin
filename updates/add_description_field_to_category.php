<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

class AddDescriptionFieldToCategory extends Migration
{
    public function up()
    {
        Schema::table('winter_mall_categories', function ($table) {
            $table->text('description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('winter_mall_categories', function ($table) {
            $table->dropColumn(['description']);
        });
    }
}
