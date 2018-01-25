<?php

namespace Ions\Event;

/**
 * Interface EventManagerInterface
 * @package Ions\Event
 */
interface EventManagerInterface
{
    /**
     * @param $name
     * @param null $target
     * @param array $argv
     * @return mixed
     */
    public function trigger($name, $target = null, array $argv = []);

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function triggerEvent(EventInterface $event);

    /**
     * @param $name
     * @param callable $listener
     * @param int $priority
     * @return mixed
     */
    public function attach($name, callable $listener, $priority = 1);

    /**
     * @param callable $listener
     * @param null $eventName
     * @return mixed
     */
    public function detach(callable $listener, $eventName = null);

    /**
     * @param $name
     * @return mixed
     */
    public function clear($name);

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function setEvent(EventInterface $event);
}
