<?php

namespace Ions\Event;

/**
 * Interface EventInterface
 * @package Ions\Event
 */
interface EventInterface
{
    /**
     * @return mixed
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getParams();

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getParam($name, $default = null);

    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @param $params
     * @return mixed
     */
    public function setParams($params);

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setParam($name, $value);

    /**
     * @param bool $flag
     * @return mixed
     */
    public function stopPropagation($flag = true);

    /**
     * @return mixed
     */
    public function propagationIsStopped();
}
