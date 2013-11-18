<?php

namespace Bowl\Service;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class SharedService extends Service
{

    /**
     * @var mixed
     */
    private $instance = null;

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if (is_null($this->instance)) {
            $closure = $this->closure;
            $this->instance = $closure();
        }

        return $this->instance;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $closure = $this->closure;
        $this->instance = $closure();

        return $this;
    }

}