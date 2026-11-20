<?php

namespace Winter\Mall\Tests;

use System\Tests\Bootstrap\PluginTestCase as BasePluginTestCase;
use Winter\Mall\Classes\Index\Index;
use Winter\Mall\Classes\Index\Noop;

class PluginTestCase extends BasePluginTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        // The plugin's version history seeds demo data while the tables still
        // carry their pre-2.1 names, which breaks the plugin refresh performed
        // during test setup. Run the migrations in the production environment
        // to skip those seeders; the test cases create the data they need.
        $app['env'] = 'production';

        return $app;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->app['env'] = 'testing';

        // The tests never need a real search index.
        $this->app->bind(Index::class, function () {
            return new Noop();
        });
    }
}
