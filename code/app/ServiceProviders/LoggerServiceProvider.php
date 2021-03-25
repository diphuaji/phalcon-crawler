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
                $path = BASE_PATH . '/storage/logs';
                mkdir($path, 0777, true);
                $adapter = new Stream("$path/main.log");
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