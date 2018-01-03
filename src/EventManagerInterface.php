<?php

namespace Ions\Event;

/**
 * Interface EventManagerInterface
 * @package Ions\Event
 */
interface EventManagerInterface
{
    /**
     * @param $eventName
     * @param null $target
     * @param array $argv
     * @return mixed
     */
    public function trigger($eventName, $target = null, array $argv = []);

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function triggerEvent(EventInterface $event);

    /**
     * @param $eventName
     * @param callable $listener
     * @param int $priority
     * @return mixed
     */
    public function attach($eventName, callable $listener, $priority = 1);

    /**
     * @param callable $listener
     * @param null $eventName
     * @return mixed
     */
    public function detach(callable $listener, $eventName = null);

    /**
     * @param $eventName
     * @return mixed
     */
    public function clear($eventName);

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function setEvent(EventInterface $event);
}
