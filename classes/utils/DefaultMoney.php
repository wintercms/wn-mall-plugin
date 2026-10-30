<?php

namespace Winter\Mall\Classes\Utils;

use Illuminate\Support\Facades\App;
use Winter\Mall\Models\Currency;

class DefaultMoney implements Money
{
    /**
     * @var \Twig\Environment
     */
    protected $twig;

    public function __construct()
    {
        $this->twig = App::make('mall.twig.environment');
    }

    public function format(?float $value, $product = null, ?Currency $currency = null): string
    {
        $currency = $currency ?? Currency::activeCurrency();

        $value    = app(Money::class)->round($value, $currency['decimals']);
        $integers = floor($value);
        $decimals = round(($value - $integers) * 100, 0);

        return $this->render($currency['format'], [
            'price'    => $value,
            'integers' => $integers,
            'decimals' => str_pad($decimals, 2, '0', STR_PAD_LEFT),
            'product'  => $product,
            // The four keys the format's help panel documents, rather than the model that
            // carries them - a currency format has no use for the rest of it
            'currency' => [
                'code'     => $currency['code'],
                'symbol'   => $currency['symbol'],
                'decimals' => $currency['decimals'],
                'rate'     => $currency['rate'],
            ],
        ]);
    }

    public function round($value, $decimals = 2): float
    {
        return round($value / 100, $decimals ?? 2);
    }

    /**
     * Renders a currency format, which is Twig a shop manager wrote, through the plugin's
     * sandboxed environment rather than a bare one built here.
     */
    protected function render($contents, array $vars)
    {
        return $this->twig->createTemplate($contents)->render($vars);
    }
}
