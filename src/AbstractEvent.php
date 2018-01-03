<?php

namespace Ions\Event;

use ArrayObject;

/**
 * Class AbstractEvent
 * @package Ions\Event
 */
abstract class AbstractEvent
{
    /**
     * @var
     */
    protected $events;

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        if ($this->events === null) {
            $this->events = new EventManager();
        }

        return $this->events;
    }

    /**
     * @param $eventName
     * @param ArrayObject $args
     * @return ResponseCollection
     */
    protected function triggerPre($eventName, ArrayObject $args)
    {
        return $this->getEventManager()->triggerEvent(new Event($eventName . '.pre', $this, $args));
    }

    /**
     * @param $eventName
     * @param ArrayObject $args
     * @param $result
     * @return mixed|null
     */
    protected function triggerPost($eventName, ArrayObject $args, & $result)
    {
        $postEvent = new PostEvent($eventName . '.post', $this, $args, $result);
        $eventRs = $this->getEventManager()->triggerEvent($postEvent);

        return $eventRs->stopped() ? $eventRs->last() : $postEvent->getResult();
    }

    /**
     * @param $eventName
     * @param ArrayObject $args
     * @param $result
     * @param \Exception $exception
     * @return mixed|null
     */
    protected function triggerException($eventName, ArrayObject $args, & $result, \Exception $exception)
    {
        $exceptionEvent = new ExceptionEvent($eventName . '.exception', $this, $args, $result, $exception);
        $eventRs = $this->getEventManager()->triggerEvent($exceptionEvent);

        if ($exceptionEvent->getThrowException()) {
            throw $exceptionEvent->getException();
        }

        return $eventRs->stopped() ? $eventRs->last() : $exceptionEvent->getResult();
    }
}
