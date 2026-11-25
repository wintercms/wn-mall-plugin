<?php

namespace Winter\Mall\Tests;

use Winter\Mall\Classes\Index\Index;
use Winter\Mall\Classes\Index\Noop;
use System\Tests\Bootstrap\PluginTestCase as BasePluginTestCase;

abstract class PluginTestCase extends BasePluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        app()->bind(Index::class, function() {
            return new Noop();
        });
    }
}
