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
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $this->event = new Event();
    }

    /**
     * @param EventInterface $event
     * @return void
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
     * @param null $name
     * @param bool $force
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function detach(callable $listener, $name = null, $force = false)
    {
        if (null === $name || ('*' === $name && !$force)) {

            foreach (array_keys($this->events) as $value) {
                $this->detach($listener, $value, true);
            }

            return true;
        }

        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects a string for the event; received %s',
                __METHOD__,
                is_object($name) ? get_class($name) : gettype($name)
            ));
        }

        if (!isset($this->events[$name])) {
            return false;
        }

        foreach ($this->events[$name] as $priority => $listeners) {
            foreach ($listeners as $index => $evaluatedListener) {

                unset($this->events[$name][$priority][$index]);

                if (empty($this->events[$name][$priority])) {
                    unset($this->events[$name][$priority]);
                    break;
                }

            }
        }

        if (empty($this->events[$name])) {
            unset($this->events[$name]);
        }

        return true;
    }

    /**
     * @param $name
     * @param null $target
     * @param array $argv
     * @return Collection
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function trigger($name, $target = null, array $argv = [])
    {
        $event = clone $this->event;

        $event->setName($name);

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
     * @return Collection
     * @throws \RuntimeException
     */
    public function triggerEvent(EventInterface $event)
    {
        return $this->triggerListeners($event);
    }

    /**
     * @param EventInterface $event
     * @param callable|null $callback
     * @return Collection
     * @throws \RuntimeException
     */
    protected function triggerListeners(EventInterface $event, callable $callback = null)
    {
        $name = $event->getName();

        if (!$name) {
            throw new \RuntimeException('Event is missing a name; cannot trigger!');
        }

        if (isset($this->events[$name])) {
            $listOfListenersByPriority = $this->events[$name];

            if (isset($this->events['*'])) {
                foreach ((array) $this->events['*'] as $priority => $listOfListeners) {
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
        $collection = new Collection();

        foreach ($listOfListenersByPriority as $listeners) {
            foreach ($listeners as $listener) {
                $result = $listener($event);
                $collection->push($result);

                if ($result !== null && !($result instanceof \Exception) && $event->propagationIsStopped()) {
                    return $result;
                }

                if ($event->propagationIsStopped()) {
                    $collection->setStopped(true);
                    return $collection;
                }

                if ($callback && $callback($result)) {
                    $collection->setStopped(true);
                    return $collection;
                }
            }
        }

        return $collection;
    }

    /**
     * @param callable $callback
     * @param $name
     * @param null $target
     * @param array $argv
     * @return Collection
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function triggerUntil(callable $callback, $name, $target = null, array $argv = [])
    {
        $event = clone $this->event;

        $event->setName($name);

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
     * @return Collection
     * @throws \RuntimeException
     */
    public function triggerEventUntil(callable $callback, EventInterface $event)
    {
        return $this->triggerListeners($event, $callback);
    }

    /**
     * @param $trigger
     * @return void
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
