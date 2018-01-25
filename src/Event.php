<?php

namespace Ions\Event;

use ArrayAccess;

/**
 * Class Event
 * @package Ions\Event
 */
class Event implements EventInterface
{
    /**
     * @var string $name
     */
    protected $name;
    /**
     * @var
     */
    protected $target;
    /**
     * @var array
     */
    protected $params = [];
    /**
     * @var bool
     */
    protected $stopPropagation = false;

    /**
     * Event constructor.
     * @param null $name
     * @param null $target
     * @param null $params
     * @throws \InvalidArgumentException
     */
    public function __construct($name = null, $target = null, $params = null)
    {
        if (null !== $name) {
            $this->setName($name);
        }
        if (null !== $target) {
            $this->setTarget($target);
        }
        if (null !== $params) {
            $this->setParams($params);
        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param $params
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setParams($params)
    {
        if (!is_array($params) && !is_object($params)) {
            throw new \InvalidArgumentException(sprintf(
                'Event parameters must be an array or object; received "%s"',
                    gettype($params)
            ));
        }

        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getParam($name, $default = null)
    {
        if (is_array($this->params) || $this->params instanceof ArrayAccess) {
            if (!isset($this->params[$name])) {
                return $default;
            }

            return $this->params[$name];
        }

        if (!isset($this->params->{$name})) {
            return $default;
        }

        return $this->params->{$name};
    }

    /**
     * @param $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * @param $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function setParam($name, $value)
    {
        if (is_array($this->params) || $this->params instanceof ArrayAccess) {
            $this->params[$name] = $value;
            return;
        }
        $this->params->{$name} = $value;
    }

    /**
     * @param bool $flag
     * @return void
     */
    public function stopPropagation($flag = true)
    {
        $this->stopPropagation = (bool)$flag;
    }

    /**
     * @return bool
     */
    public function propagationIsStopped()
    {
        return $this->stopPropagation;
    }
}
