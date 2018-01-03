<?php

namespace Ions\Event;

use ArrayObject;
use Exception;

/**
 * Class ExceptionEvent
 * @package Ions\Event
 */
class ExceptionEvent extends PostEvent
{
    /**
     * @var
     */
    protected $exception;

    /**
     * @var bool
     */
    protected $throwException = true;

    /**
     * ExceptionEvent constructor.
     * @param null $name
     * @param null $target
     * @param ArrayObject $params
     * @param $result
     * @param Exception $exception
     */
    public function __construct($name, $target, ArrayObject $params, & $result, Exception $exception)
    {
        parent::__construct($name, $target, $params, $result);
        $this->setException($exception);
    }

    /**
     * @param Exception $exception
     * @return $this
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param $flag
     * @return $this
     */
    public function setThrowException($flag)
    {
        $this->throwException = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getThrowException()
    {
        return $this->throwException;
    }
}
