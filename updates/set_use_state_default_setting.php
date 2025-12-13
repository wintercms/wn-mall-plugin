<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Winter\Mall\Models\GeneralSettings;
use Schema;

class SetUseStateDefaultSetting extends Migration
{
    public function up()
    {
        // To remain backwards compatible this setting is set to true.
        GeneralSettings::set('use_state', true);
    }

    public function down()
    {
        // Do nothing.
    }
}
