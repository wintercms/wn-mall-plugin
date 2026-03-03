<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('winter_mall_categories', function ($table) {
            $table->boolean('published')->default(true);
        });
    }

    public function down()
    {
        Schema::table('winter_mall_categories', function ($table) {
            $table->dropColumn('published');
        });
    }
};
