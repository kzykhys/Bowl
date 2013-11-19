<?php

namespace Bowl\Service;

/**
 * Manage services
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
abstract class Service implements ServiceInterface
{

    /**
     * @var callable
     */
    protected $closure;

    /**
     * @param \Closure $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @param callable $closure
     *
     * @return $this
     */
    public function setClosure(\Closure $closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * @return \Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }

} 