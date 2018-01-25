<?php

namespace Ions\Event;

/**
 * Class Listener
 * @package Ions\Event
 */
trait ListenerTrait
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            $events->detach($callback);
            unset($this->listeners[$index]);
        }
    }
}
