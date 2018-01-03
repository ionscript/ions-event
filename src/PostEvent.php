<?php

namespace Ions\Event;

use ArrayObject;

/**
 * Class PostEvent
 * @package Ions\Event
 */
class PostEvent extends Event
{
    /**
     * @var
     */
    protected $result;

    /**
     * PostEvent constructor.
     * @param null $name
     * @param null $target
     * @param ArrayObject $params
     * @param $result
     */
    public function __construct($name, $target, ArrayObject $params, & $result)
    {
        parent::__construct($name, $target, $params);
        $this->setResult($result);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setResult(& $value)
    {
        $this->result = & $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function & getResult()
    {
        return $this->result;
    }
}
