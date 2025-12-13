<?php namespace Winter\Mall\Updates;

use Artisan;
use DB;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class AddPDFPartialToPaymentMethod extends Migration
{
    public function up()
    {
        Schema::table('offline_mall_payment_methods', function ($table) {
            $table->string('pdf_partial')->after('fee_percentage')->nullable();
        });
    }

    public function down()
    {
        Schema::table('offline_mall_payment_methods', function ($table) {
            $table->dropColumn(['pdf_partial']);
        });
    }
}