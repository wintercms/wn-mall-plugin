<?php

namespace Winter\Mall\Classes\Utils;

use Winter\Mall\Models\Currency;

interface Money
{
    public function format(?float $value, $product = null, ?Currency $currency = null): string;

    public function round($value, $decimals = 2): float;
}
