<?php

namespace Winter\Mall\Classes\Registration;

use Barryvdh\DomPDF\Facade;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Hashids\Hashids;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Cache;
use Winter\Mall\Classes\Customer\AuthManager;
use Winter\Mall\Classes\Customer\DefaultSignInHandler;
use Winter\Mall\Classes\Customer\DefaultSignUpHandler;
use Winter\Mall\Classes\Customer\SignInHandler;
use Winter\Mall\Classes\Customer\SignUpHandler;
use Winter\Mall\Classes\Index\Filebase;
use Winter\Mall\Classes\Index\Index;
use Winter\Mall\Classes\Index\IndexNotSupportedException;
use Winter\Mall\Classes\Index\MySQL\MySQL;
use Winter\Mall\Classes\Payments\DefaultPaymentGateway;
use Winter\Mall\Classes\Payments\Offline;
use Winter\Mall\Classes\Payments\PaymentGateway;
use Winter\Mall\Classes\Payments\PayPalRest;
use Winter\Mall\Classes\Payments\PostFinance;
use Winter\Mall\Classes\Payments\Stripe;
use Winter\Mall\Classes\Utils\DefaultMoney;
use Winter\Mall\Classes\Utils\Money;
use Winter\Mall\Models\GeneralSettings;

trait BootServiceContainer
{
    protected function registerServices()
    {
        $this->app->bind(SignInHandler::class, function () {
            return new DefaultSignInHandler();
        });
        $this->app->bind(SignUpHandler::class, function () {
            return new DefaultSignUpHandler();
        });
        $this->app->singleton(Money::class, function () {
            return new DefaultMoney();
        });
        $this->app->singleton(PaymentGateway::class, function () {
            $gateway = new DefaultPaymentGateway();
            $gateway->registerProvider(new Offline());
            $gateway->registerProvider(new PayPalRest());
            $gateway->registerProvider(new Stripe());
            $gateway->registerProvider(new PostFinance());

            return $gateway;
        });
        $this->app->singleton(Hashids::class, function () {
            return new Hashids(config('app.key', 'oc-mall'), 8);
        });
        $this->app->singleton('user.auth', function () {
            return AuthManager::instance();
        });
        $this->app->bind(Index::class, function () {
            $driver = Cache::rememberForever('winter_mall.mysql.index.driver', function () {
                $driver = GeneralSettings::get('index_driver');
                if ($driver === null) {
                    GeneralSettings::set('index_driver', 'database');
                }

                return $driver;
            });
            try {
                if ($driver === 'filesystem') {
                    return new Filebase();
                }

                return new MySQL();
            } catch (IndexNotSupportedException $e) {
                logger()->error(
                    '[Winter.Mall] Your database does not support JSON data. Your index driver has been switched to "Filesystem". Update your database to make use of database indexing.'
                );
                GeneralSettings::set('index_driver', 'filesystem');
                Cache::forget('winter_mall.mysql.index.driver');

                return new Filebase();
            }
        });

        $this->registerDomPDF();
    }

    /**
     * Register barryvdh/laravel-dompdf
     */
    protected function registerDomPDF()
    {
        AliasLoader::getInstance()->alias('PDF', Facade::class);

        $this->app->bind('dompdf.options', function () {
            if ($defines = $this->app['config']->get('winter.mall::pdf.defines')) {
                $options = [];
                foreach ($defines as $key => $value) {
                    $key           = strtolower(str_replace('DOMPDF_', '', $key));
                    $options[$key] = $value;
                }
            } else {
                $options = $this->app['config']->get('winter.mall::pdf.options', []);
            }

            return $options;
        });

        $this->app->bind('dompdf', function () {
            $options = $this->app->make('dompdf.options');
            $dompdf  = new Dompdf($options);
            $dompdf->setBasePath(realpath(base_path('public')));

            return $dompdf;
        });
        $this->app->alias('dompdf', Dompdf::class);
        $this->app->bind('dompdf.wrapper', function ($app) {
            return new PDF($app['dompdf'], $app['config'], $app['files'], $app['view']);
        });
    }
}
