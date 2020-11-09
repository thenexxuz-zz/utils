<?php

namespace TheNexxuz\Utils;

/**
 * Use this class to measure response time when debugging or to include time in your API responses
 * Just create an object has this class type and
 * var_dump object->mark to get the number of seconds and microseconds
 *
 * Class MeasureTime
 */
class MeasureTime
{
    private $time = null;
    private $lastMark = null;
    private $decimals = 6;

    /**
     * Will set the initial time with microseconds
     *
     * MeasureTime constructor.
     */
    public function __construct()
    {
        $this->time = microtime(true);
    }

    /**
     * Will return the time in seconds with microseconds between the object was created and when this method is called
     * @return mixed
     */
    public function mark()
    {
        $now = microtime(true);
        $this->lastMark = $now;

        return number_format($now - $this->time, $this->decimals);
    }

    /**
     * Will return the time in seconds with microseconds between last mark or markInterval and when this method is called
     * @return mixed
     */
    public function markInterval()
    {
        if (empty($this->lastMark)) {
            return $this->mark();
        }
        $now = microtime(true);
        $interval = $now - $this->lastMark;
        $this->lastMark = $now;

        return number_format($interval, $this->decimals);
    }
}