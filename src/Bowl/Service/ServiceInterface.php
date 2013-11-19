<?php

namespace Bowl\Service;

/**
 * Interface of service definition
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
interface ServiceInterface
{

    /**
     * @return mixed
     */
    public function get();

    /**
     * @return ServiceInterface
     */
    public function reset();

    /**
     * @param callable $closure
     *
     * @return $this
     */
    public function setClosure(\Closure $closure);

    /**
     * @return \Closure
     */
    public function getClosure();

} 