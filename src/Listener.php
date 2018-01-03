<?php

namespace Ions\Event;

/**
 * Class Listener
 * @package Ions\Event
 */
abstract class Listener implements ListenerInterface
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            $events->detach($callback);
            unset($this->listeners[$index]);
        }
    }
}
