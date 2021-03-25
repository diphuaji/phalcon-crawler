<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use App\EventRegister;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerNamespaces(
    [
        'App' => APP_PATH,
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

// register services
$serviceProviders = include(BASE_PATH.'/config/service_providers.php');
foreach ($serviceProviders as $serviceProvider) {
    $container->register(new $serviceProvider());
}

// register events
(new EventRegister())->registerEvents();

$application = new Application($container);


try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Throwable $e) {
    echo 'Exception: ', $e->getMessage();
    var_dump($e->getTrace());
}