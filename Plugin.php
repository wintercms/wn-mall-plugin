<?php namespace Winter\Mall;


use Illuminate\Support\Facades\View;
use Winter\Mall\Classes\Registration\BootComponents;
use Winter\Mall\Classes\Registration\BootEvents;
use Winter\Mall\Classes\Registration\BootExtensions;
use Winter\Mall\Classes\Registration\BootMails;
use Winter\Mall\Classes\Registration\BootRelations;
use Winter\Mall\Classes\Registration\BootServiceContainer;
use Winter\Mall\Classes\Registration\BootSettings;
use Winter\Mall\Classes\Registration\BootTwig;
use Winter\Mall\Classes\Registration\BootValidation;
use Winter\Mall\Console\Initialize;
use Winter\Mall\Console\ReindexProducts;
use Winter\Mall\Console\SeedDemoData;
use Winter\Mall\Console\SystemCheck;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['Winter.User', 'Winter.Location', 'Winter.Translate'];

    use BootEvents;
    use BootExtensions;
    use BootServiceContainer;
    use BootSettings;
    use BootComponents;
    use BootMails;
    use BootValidation;
    use BootTwig;
    use BootRelations;

    public function __construct($app)
    {
        parent::__construct($app);
        // The morph map has to be registered in the constructor so it is available
        // when plugin migrations are run.
        $this->registerRelations();
    }

    public function pluginDetails()
    {
        return [
            'name' => 'winter.mall::lang.plugin.name',
            'description' => 'winter.mall::lang.plugin.description',
            'author' => 'OFFLINE GmbH',
            'icon' => 'oc-icon-shopping-cart',
            'homepage' => '',
            'replaces' => ['OFFLINE.Mall' => '~2.0'],
        ];
    }

    public function register()
    {
        $this->registerServices();
        $this->registerTwigEnvironment();
    }

    public function boot()
    {
        $this->registerExtensions();
        $this->registerEvents();
        $this->registerValidationRules();

        $this->registerConsoleCommand('winter.mall.seed-demo', SeedDemoData::class);
        $this->registerConsoleCommand('winter.mall.reindex', ReindexProducts::class);
        $this->registerConsoleCommand('winter.mall.system-check', SystemCheck::class);
        $this->registerConsoleCommand('winter.mall.initialize', Initialize::class);

        View::share('app_url', config('app.url'));
    }
}
