<?php

namespace Winter\Mall\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Winter\Mall\Classes\Index\Index;
use Winter\Mall\Classes\Index\Noop;
use System\Classes\PluginManager;

class PluginTestCase extends \PluginTestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        // System\ServiceProvider sets this while the test database is still empty, and
        // registerPlugin() returns on it before it reaches register(), so without clearing it
        // registerAll() leaves every binding a plugin declares unbound
        PluginManager::$noInit = false;

        $pluginManager = PluginManager::instance();
        $pluginManager->registerAll(true);
        $pluginManager->bootAll(true);

        app()->bind(Index::class, function() {
            return new Noop();
        });
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $pluginManager = PluginManager::instance();
        $pluginManager->unregisterAll();
    }
}
