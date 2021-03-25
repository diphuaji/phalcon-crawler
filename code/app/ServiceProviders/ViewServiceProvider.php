<?php

namespace App\ServiceProviders;

use Phalcon\Mvc\View;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class ViewServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->set(
            'view',
            function () {
                $view = new View();

                $view->setViewsDir('../app/views/');
                $view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                $view->registerEngines(
                    [
                        '.volt' => 'voltService',
                    ]
                );

                return $view;
            }
        );
    }
}