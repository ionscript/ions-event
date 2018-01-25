<?php

namespace Ions\Event;

use SplStack;

/**
 * Class Collection
 * @package Ions\Event
 */
class Collection extends SplStack
{
    /**
     * @var bool
     */
    protected $stopped = false;

    /**
     * @return bool
     */
    public function stopped()
    {
        return $this->stopped;
    }

    /**
     * @param $flag
     */
    public function setStopped($flag)
    {
        $this->stopped = (bool) $flag;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return parent::bottom();
    }

    /**
     * @return mixed|null
     */
    public function last()
    {
        if (count($this) === 0) {
            return null;
        }
        return parent::top();
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value)
    {
        foreach ($this as $response) {
            if ($response === $value) {
                return true;
            }
        }

        return false;
    }
}
