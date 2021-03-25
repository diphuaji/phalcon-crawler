<?php

namespace App\ServiceProviders;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Di\ServiceProviderInterface;

class VoltServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared(
            'voltService',
            function (ViewBaseInterface $view) use ($di) {
                $volt = new Volt($view, $di);
                $volt->setOptions(
                    [
                        'always' => true,
                        'extension' => '.php',
                        'separator' => '_',
                        'stat' => true,
                        'path' => BASE_PATH . '/storage/cache/volt/',
                        'prefix' => 'compiled'
                    ]
                );

                return $volt;
            }
        );
    }
}