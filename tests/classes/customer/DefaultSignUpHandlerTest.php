<?php namespace Winter\Mall\Tests\Classes\Customer;

use Winter\Mall\Classes\Customer\DefaultSignUpHandler;
use Winter\Mall\Models\GeneralSettings;
use Winter\Mall\Tests\PluginTestCase;

class DefaultSignUpHandlerTest extends PluginTestCase
{
    public function test_it_does_not_require_phone_by_default()
    {
        GeneralSettings::set('address_phone_required_billing', false);
        GeneralSettings::set('address_phone_required_shipping', false);

        $rules = DefaultSignUpHandler::rules();

        $this->assertFalse(array_key_exists('billing_phone', $rules));
        $this->assertFalse(array_key_exists('shipping_phone', $rules));
    }

    public function test_it_adds_phone_rules_when_settings_are_enabled()
    {
        GeneralSettings::set('address_phone_required_billing', true);
        GeneralSettings::set('address_phone_required_shipping', true);

        $rules = DefaultSignUpHandler::rules();

        $this->assertTrue(array_key_exists('billing_phone', $rules));
        $this->assertSame('required', $rules['billing_phone']);

        $this->assertTrue(array_key_exists('shipping_phone', $rules));
        $this->assertSame('required_if:use_different_shipping,1', $rules['shipping_phone']);
    }

    public function tearDown(): void
    {
        GeneralSettings::set('address_phone_required_billing', false);
        GeneralSettings::set('address_phone_required_shipping', false);

        parent::tearDown();
    }
}
