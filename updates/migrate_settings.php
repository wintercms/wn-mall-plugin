<?php namespace Winter\Mall\Updates;

use Winter\Storm\Database\Updates\Migration;
use Winter\Mall\Classes\Payments\PaymentGateway;
use Winter\Mall\Models\GeneralSettings;
use Winter\Mall\Models\FeedSettings;
use Winter\Mall\Models\PaymentGatewaySettings;
use Winter\Mall\Models\ReviewSettings;

class MigrateSettings extends Migration
{
    public function up()
    {
        $gw = app(PaymentGateway::class);
        foreach ($gw->getProviders() as $provider) {
            foreach (array_keys($provider->settings()) as $key) {
                $value = GeneralSettings::get($key);
                PaymentGatewaySettings::set($key, $value);
            }
        }
        foreach (['google_merchant_enabled', 'google_merchant_key'] as $key) {
            $value = GeneralSettings::get($key);
            FeedSettings::set($key, $value);
        }
        foreach (['enabled', 'moderated', 'allow_anonymous'] as $key) {
            $value = GeneralSettings::get($key);
            ReviewSettings::set($key, $value);
        }
    }

    public function down()
    {
        $gw = app(PaymentGateway::class);
        foreach ($gw->getProviders() as $provider) {
            foreach (array_keys($provider->settings()) as $key) {
                $value = PaymentGatewaySettings::get($key);
                $value = GeneralSettings::set($key, $value);
            }
        }
        foreach (['google_merchant_enabled', 'google_merchant_key'] as $key) {
            $value = FeedSettings::get($key);
            GeneralSettings::set($key, $value);
        }
        foreach (['enabled', 'moderated', 'allow_anonymous'] as $key) {
            $value = ReviewSettings::get($key);
            GeneralSettings::set($key, $value);
        }
    }
}
