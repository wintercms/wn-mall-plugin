<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

return new class extends Migration
{
    protected string $table = 'winter_mall_addresses';

    public function up()
    {
        if (!Schema::hasTable($this->table) || Schema::hasColumn($this->table, 'phone')) {
            return;
        }

        Schema::table($this->table, function ($table) {
            $table->string('phone')->nullable()->after('city');
        });
    }

    public function down()
    {
        if (!Schema::hasTable($this->table) || !Schema::hasColumn($this->table, 'phone')) {
            return;
        }

        Schema::table($this->table, function ($table) {
            $table->dropColumn('phone');
        });
    }
};