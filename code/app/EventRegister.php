<?php

namespace App;

use Phalcon\Di\Injectable;
use App\Listeners\LoggingEventListener;

class EventRegister extends Injectable
{

    public function registerEvents(): void
    {
        $eventsManager = $this->di->get('eventsManager');
        $eventsManager->attach(
            'logging',
            new LoggingEventListener()
        );
    }
}