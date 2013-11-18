<?php

namespace Bowl\Service;

/**
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

} 