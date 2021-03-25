<?php

namespace App\ServiceProviders;

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group as RouterGroup;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class RouterServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $router = new Router();
        $group = new RouterGroup(
            [
                'controller' => 'index',
                'namespace' => 'App\Controllers'
            ]
        );
        $group->setPrefix('/');

        $group->add(
            '',
            [
                'action' => 'index',
            ]
        );

        $group->add(
            '/edit/{id}',
            [
                'action' => 'edit',
            ]
        );

        $group->add(
            '/view',
            [
                'controller' => 'common',
                'action' => 'index',
            ]
        );

        $router->mount($group);

        $di->set('router', $router);
    }
}