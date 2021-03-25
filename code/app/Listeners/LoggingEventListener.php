<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Logger;
use Phalcon\Events\Event;

/**
 * @property Logger $logger
 */
class LoggingEventListener extends Injectable
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct()
    {
        $this->logger = $this->di->get('logger');
    }

    public function warning(Event $event, $component, $message): void
    {
        $this->logger->warning($message);
    }
}