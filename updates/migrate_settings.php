<?php namespace Winter\Mall\Updates;

use App;
use DB;
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

        GeneralSettings::firstWhere('item', 'offline_mall_settings')->update(['item' => 'winter_mall_settings']);

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
        $srcPaymentGatewaySettings->resetDefault();

        foreach (['google_merchant_enabled', 'google_merchant_key'] as $key) {
            $value = FeedSettings::get($key);
            GeneralSettings::set($key, $value);
        }
        FeedSettings::resetDefault();

        foreach (['enabled', 'moderated', 'allow_anonymous'] as $key) {
            $value = ReviewSettings::get($key);
            GeneralSettings::set($key, $value);
        }
        ReviewSettings::resetDefault();

        DB::table('system_settings')->where('item', 'winter_mall_settings')->update(['item' => 'offline_mall_settings']);
    }
}
