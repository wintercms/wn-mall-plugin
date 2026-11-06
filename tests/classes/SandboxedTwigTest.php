<?php namespace Winter\Mall\Tests\Classes;

use Twig\Extension\SandboxExtension;
use Twig\Sandbox\SecurityError;
use Winter\Mall\Classes\Utils\Money;
use Winter\Mall\Models\Currency;
use Winter\Mall\Models\PaymentMethod;
use Winter\Mall\Tests\PluginTestCase;

/**
 * Both of the Twig templates this plugin renders - a currency's format and a payment method's
 * instructions - are written in a `codeeditor` field by whoever runs the shop, who needs no code
 * permission to do it. They were safe only because Winter's Twig sandbox used to be
 * unconditional, so these cover that the plugin now asks for a sandbox of its own.
 */
class SandboxedTwigTest extends PluginTestCase
{
    protected function currency(string $format): Currency
    {
        return new Currency([
            'id'       => 42,
            'code'     => 'EUR',
            'symbol'   => '€',
            'rate'     => 1,
            'decimals' => 2,
            'format'   => $format,
        ]);
    }

    protected function paymentMethod(string $instructions): PaymentMethod
    {
        $method = new PaymentMethod();
        $method->name = 'Invoice';
        $method->payment_provider = 'offline';
        $method->instructions = $instructions;

        return $method;
    }

    public function test_the_plugin_twig_environment_is_sandboxed()
    {
        $this->assertTrue(app('mall.twig.environment')->hasExtension(SandboxExtension::class));
    }

    public function test_a_currency_format_still_renders()
    {
        $format = '{{ currency.code }} {{ price|number_format(currency.decimals, ".", ",") }}';

        $this->assertEquals(
            'EUR 1,500.40',
            app(Money::class)->format(150040, null, $this->currency($format))
        );
    }

    public function test_a_currency_format_sees_the_documented_keys_and_not_the_model()
    {
        $this->assertEquals(
            '[EUR][2][]',
            app(Money::class)->format(
                100,
                null,
                $this->currency('[{{ currency.code }}][{{ currency.decimals }}][{{ currency.id }}]')
            )
        );
    }

    public function test_a_currency_format_cannot_reach_beyond_its_variables()
    {
        $this->expectException(SecurityError::class);

        app(Money::class)->format(100, null, $this->currency('{{ str_random(8) }}'));
    }

    public function test_payment_instructions_still_render()
    {
        $this->assertEquals(
            'Pay now, reference [].',
            $this->paymentMethod('Pay now, reference [{{ order.order_number }}].')->renderInstructions()
        );
    }

    public function test_payment_instructions_cannot_reach_beyond_their_variables()
    {
        $this->expectException(SecurityError::class);

        $this->paymentMethod("{{ source('.env') }}")->renderInstructions();
    }
}
