<?php

namespace Bowl;

use Bowl\Service\Service;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class TaggedServices implements \Iterator
{

    /**
     * @var Service[]
     */
    private $services = [];

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @param Service $service
     */
    public function add(Service $service)
    {
        $this->services[] = $service;
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->services[$this->position]->get();
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->services[$this->position]);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 0;
    }

}