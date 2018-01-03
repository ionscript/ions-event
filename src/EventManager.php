<?php

namespace Ions\Event;

/**
 * Class EventManager
 * @package Ions\Event
 */
class EventManager implements EventManagerInterface
{
    /**
     * @var array
     */
    protected $events = [];

    /**
     * @var Event
     */
    protected $event;

    /**
     * EventManager constructor.
     */
    public function __construct()
    {
        $this->event = new Event();
    }

    /**
     * @param EventInterface $event
     */
    public function setEvent(EventInterface $event)
    {
        $this->event = $event;
    }

    /**
     * @param $trigger
     * @param callable $listener
     * @param int $priority
     * @return callable
     * @throws \InvalidArgumentException
     */
    public function attach($trigger, callable $listener, $priority = 0)
    {
        if (!is_string($trigger)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects a string for the event; received %s',
                __METHOD__,
                is_object($trigger) ? get_class($trigger) : gettype($trigger)
            ));
        }

        $this->events[$trigger][(int)$priority][] = $listener;

        return $listener;
    }

    /**
     * @param callable $listener
     * @param null $eventName
     * @param bool $force
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function detach(callable $listener, $eventName = null, $force = false)
    {
        if (null === $eventName || ('*' === $eventName && !$force)) {

            foreach (array_keys($this->events) as $name) {
                $this->detach($listener, $name, true);
            }

            return true;
        }

        if (!is_string($eventName)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects a string for the event; received %s',
                __METHOD__,
                is_object($eventName) ? get_class($eventName) : gettype($eventName)
            ));
        }

        if (!isset($this->events[$eventName])) {
            return false;
        }

        foreach ($this->events[$eventName] as $priority => $listeners) {
            foreach ($listeners[0] as $index => $evaluatedListener) {

                if ($evaluatedListener !== $listener) {
                    continue;
                }

                unset($this->events[$eventName][$priority][0][$index]);

                if (empty($this->events[$eventName][$priority][0])) {
                    unset($this->events[$eventName][$priority]);
                    break;
                }

            }
        }

        if (empty($this->events[$eventName])) {
            unset($this->events[$eventName]);
        }

        return true;
    }

    /**
     * @param $eventName
     * @param null $target
     * @param array $argv
     * @return ResponseCollection
     */
    public function trigger($eventName, $target = null, array $argv = [])
    {
        $event = clone $this->event;

        $event->setName($eventName);

        if ($target !== null) {
            $event->setTarget($target);
        }

        if ($argv) {
            $event->setParams($argv);
        }

        return $this->triggerListeners($event);
    }

    /**
     * @param EventInterface $event
     * @return ResponseCollection
     */
    public function triggerEvent(EventInterface $event)
    {
        return $this->triggerListeners($event);
    }

    /**
     * @param EventInterface $event
     * @param callable|null $callback
     * @return ResponseCollection
     * @throws \RuntimeException
     */
    protected function triggerListeners(EventInterface $event, callable $callback = null)
    {
        $name = $event->getName();

        if (empty($name)) {
            throw new \RuntimeException('Event is missing a name; cannot trigger!');
        }

        if (isset($this->events[$name])) {
            $listOfListenersByPriority = $this->events[$name];

            if (isset($this->events['*'])) {
                foreach ($this->events['*'] as $priority => $listOfListeners) {
                    $listOfListenersByPriority[$priority][] = $listOfListeners[0];
                }
            }

        } elseif (isset($this->events['*'])) {
            $listOfListenersByPriority = $this->events['*'];
        } else {
            $listOfListenersByPriority = [];
        }

        krsort($listOfListenersByPriority);

        $event->stopPropagation(false);

        // Execute listeners
        $responses = new ResponseCollection();

        foreach ($listOfListenersByPriority as $listeners) {
            foreach ($listeners as $listener) {
                $response = $listener($event);
                $responses->push($response);

                if ($response !== null && !($response instanceof \Exception) && $event->propagationIsStopped()) {
                    return $response;
                }

                // If the event was asked to stop propagating, do so
                if ($event->propagationIsStopped()) {
                    $responses->setStopped(true);
                    return $responses;
                }

                // If the result causes our validation callback to return true,
                // stop propagation
                if ($callback && $callback($response)) {
                    $responses->setStopped(true);
                    return $responses;
                }
            }
        }

        return $responses;
    }

    /**
     * @param callable $callback
     * @param $eventName
     * @param null $target
     * @param array $argv
     * @return ResponseCollection
     */
    public function triggerUntil(callable $callback, $eventName, $target = null, array $argv = [])
    {
        $event = clone $this->event;

        $event->setName($eventName);

        if ($target !== null) {
            $event->setTarget($target);
        }

        if ($argv) {
            $event->setParams($argv);
        }

        return $this->triggerListeners($event, $callback);
    }

    /**
     * @param callable $callback
     * @param EventInterface $event
     * @return ResponseCollection
     */
    public function triggerEventUntil(callable $callback, EventInterface $event)
    {
        return $this->triggerListeners($event, $callback);
    }

    /**
     * @param $trigger
     */
    public function clear($trigger)
    {
        if (isset($this->events[$trigger])) {
            unset($this->events[$trigger]);
        }
    }

    /**
     * @param array $args
     * @return \ArrayObject
     */
    public function prepareArgs(array $args)
    {
        return new \ArrayObject($args);
    }
}
