<?php

namespace Winter\Mall\Classes\Registration;

use Winter\Mall\Classes\Utils\Money;
use System\Classes\MarkupManager;

trait BootTwig
{
    /**
     * Registers the Twig environment the plugin renders its user-written templates in.
     *
     * A currency's format and a payment method's instructions are both Twig, written in a
     * `codeeditor` field by whoever runs the shop, who needs no code permission to do it. So the
     * environment is sandboxed: a template there can read the variables it is passed and format
     * them, and nothing else - no wildcard markup extension, no method call on a model, no
     * reading another template off disk.
     */
    public function registerTwigEnvironment()
    {
        $this->app->singleton('mall.twig.environment', function () {
            return MarkupManager::makeSandboxedTwigEnvironment([
                // The filters registerMarkupTags() below adds
                'filters' => ['money', '_', '__'],
            ]);
        });
    }

    public function registerMarkupTags()
    {
        $filters = [
            'money' => function (...$args) {
                return app(Money::class)->format(...$args);
            },
        ];

        // Check the translate plugin is installed
        if ( ! class_exists('Winter\Translate\Behaviors\TranslatableModel')) {
            $filters['_']  = ['Lang', 'get'];
            $filters['__'] = ['Lang', 'choice'];
        }

        return [
            'filters' => $filters,
        ];
    }
}
