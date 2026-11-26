<?php

declare(strict_types=1);

namespace Winter\Mall\Tests\Unit\Models;

use Exception;
use Winter\Mall\Models\Currency;
use Winter\Mall\Tests\PluginTestCase;
use Throwable;

class CurrencyTest extends PluginTestCase
{
    /**
     * Check if Table-Seeder sets the default currency correctly.
     * 
     * @return void
     */
    public function test_has_one_default_currency()
    {
        $cur = Currency::where('is_default', 1)->count();
        $this->assertEquals($cur, 1);

        return $cur;
    }

    /**
     * Only one currency can be the default currency.
     * 
     * @return void
     */
    public function test_only_one_default_currency()
    {
        $oldDefaultCurrencyId = Currency::defaultCurrency()->id;

        // Set new currency
        $currency = Currency::where('is_default', 0)->first();
        $currency->is_default = true;
        $this->assertTrue($currency->save(), 'Currency could not be updated.');

        // Check if new currency is the default one
        $default = Currency::where('is_default', 1)->first();
        $this->assertNotEmpty($default);
        $this->assertEquals($default->id, $currency->id);
        $this->assertNotEquals($default->id, $oldDefaultCurrencyId);

        return $currency;
    }
}