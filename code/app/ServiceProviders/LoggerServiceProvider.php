<?php

namespace App\ServiceProviders;

use Phalcon\Di\DiInterface;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Di\ServiceProviderInterface;

class LoggerServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->set(
            'logger',
            function () {
                $adapter = new Stream(BASE_PATH.'/storage/logs/main.log');
                $logger  = new Logger(
                    'messages',
                    [
                        'main' => $adapter,
                    ]
                );

                return $logger;
            }
        );
    }
}