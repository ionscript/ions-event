<?php

namespace Ions\Event;

use Ions\Mvc\ServiceManager;

/**
 * Interface ListenerInterface
 * @package Ions\Event
 */
interface ListenerInterface
{
    /**
     * @param ServiceManager $serviceManager
     * @param EventManagerInterface $events
     * @param int $priority
     * @return mixed
     */
    public function attach(ServiceManager $serviceManager, EventManagerInterface $events, $priority = 1);

    /**
     * @param EventManagerInterface $events
     * @return mixed
     */
    public function detach(EventManagerInterface $events);
}
