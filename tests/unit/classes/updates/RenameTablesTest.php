<?php

namespace Winter\Mall\Tests\Unit\Classes\Updates;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Winter\Mall\Tests\PluginTestCase;
use Winter\Mall\Updates\RenameTables;

class RenameTablesTest extends PluginTestCase
{
    /**
     * Check table renaming and data preservation in both migration directions.
     *
     * @return void
     */
    public function test_it_renames_tables_in_both_directions_on_the_test_connection()
    {
        Schema::create('winter_mall_rename_test', function (Blueprint $table) {
            $table->integer('value');
        });
        DB::table('winter_mall_rename_test')->insert(['value' => 42]);

        $migration = new RenameTables();
        $migration->down();

        $this->assertTrue(Schema::hasTable('offline_mall_rename_test'));
        $this->assertFalse(Schema::hasTable('winter_mall_rename_test'));
        $this->assertEquals(42, DB::table('offline_mall_rename_test')->value('value'));

        $migration->up();

        $this->assertTrue(Schema::hasTable('winter_mall_rename_test'));
        $this->assertFalse(Schema::hasTable('offline_mall_rename_test'));
        $this->assertEquals(42, DB::table('winter_mall_rename_test')->value('value'));
    }
}
