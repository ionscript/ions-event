<?php

namespace Ions\Event;

/**
 * Interface ListenerInterface
 * @package Ions\Event
 */
interface ListenerInterface
{
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @return mixed
     */
    public function attach(EventManagerInterface $events, $priority = 1);

    /**
     * @param EventManagerInterface $events
     * @return mixed
     */
    public function detach(EventManagerInterface $events);
}
