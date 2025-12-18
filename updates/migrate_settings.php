<?php namespace Winter\Mall\Updates;

use App;
use Cache;
use Winter\Storm\Database\Updates\Migration;
use Winter\Mall\Classes\Payments\PaymentGateway;
use Winter\Mall\Classes\Registration\BootServiceContainer;
use Winter\Mall\Models\GeneralSettings;
use Winter\Mall\Models\FeedSettings;
use Winter\Mall\Models\PaymentGatewaySettings;
use Winter\Mall\Models\ReviewSettings;

class MigrateSettings extends Migration
{
    use BootServiceContainer;

    protected $app = null;
    protected $gw = null;

    protected function init()
    {
        Cache::clear();
        $this->app = App::make('app');
        $this->registerServices();
        $this->gw = app(PaymentGateway::class);
    }

    public function up()
    {
        $this->init();

        $srcPaymentGatewaySettings = GeneralSettings::instance();
        $dstPaymentGatewaySettings = PaymentGatewaySettings::instance();

        foreach ($this->gw->getProviders() as $provider) {
            foreach (array_keys($provider->settings()) as $key) {
                $data = $srcPaymentGatewaySettings->get($key);
                try {
                    $value = decrypt($data);
                } catch (\Exception) {
                    $value = $data;
                }
                $dstPaymentGatewaySettings->set($key, $value);
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
        $this->init();

        $srcPaymentGatewaySettings = PaymentGatewaySettings::instance();
        $dstPaymentGatewaySettings = GeneralSettings::instance();

        foreach ($this->gw->getProviders() as $provider) {
            foreach (array_keys($provider->settings()) as $key) {
                $value = $srcPaymentGatewaySettings->get($key);
                $dstPaymentGatewaySettings->set($key, $value);
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
