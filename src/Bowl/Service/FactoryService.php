<?php

namespace Bowl\Service;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class FactoryService extends Service
{

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $closure = $this->closure;

        return $closure();
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function reset()
    {
        return $this;
    }

}