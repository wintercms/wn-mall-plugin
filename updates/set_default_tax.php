<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Winter\Mall\Models\Tax;
use Schema;

class SetDefaultTax extends Migration
{
    public function up()
    {
        Tax::extend(function () {
            $this->setTable('offline_mall_taxes');
        }, true);

        // Version 1.9.0 introduced a default tax. Let's use the first one as default.
        $tax = Tax::first();
        if ($tax) {
            $tax->is_default = true;
            $tax->save();
        }
    }

    public function down()
    {
        // Do nothing.
    }
}
