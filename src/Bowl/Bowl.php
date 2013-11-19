<?php

namespace Bowl;

use Bowl\Service\FactoryService;
use Bowl\Service\Service;
use Bowl\Service\SharedService;
use Bowl\Traits\Parameters;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Bowl implements \ArrayAccess
{

    use Parameters;

    /**
     * @var TaggedServices[]
     */
    private $tags = [];

    /**
     * @var Service[]
     */
    private $services = [];

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function get($name)
    {
        if (!isset($this->services[$name])) {
            throw new \InvalidArgumentException('Undefined service: ' . $name);
        }

        return $this->services[$name]->get();
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function reset($name)
    {
        $this->services[$name]->reset();

        return $this;
    }

    /**
     * @param string   $name
     * @param callable $closure
     * @param array    $tags
     *
     * @return $this
     */
    public function share($name, \Closure $closure, $tags = [])
    {
        $this->register($name, new SharedService($closure->bindTo($this)), $tags);

        return $this;
    }

    /**
     * @param string   $name
     * @param callable $closure
     * @param array    $tags
     *
     * @return $this
     */
    public function factory($name, \Closure $closure, $tags = [])
    {
        $this->register($name, new FactoryService($closure->bindTo($this)), $tags);

        return $this;
    }

    /**
     * @param string   $name
     * @param callable $closure
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function extend($name, \Closure $closure)
    {
        if (!isset($this->services[$name])) {
            throw new \InvalidArgumentException('Undefined service: ' . $name);
        }

        $parent = $this->services[$name]->getClosure();
        $extend = function () use ($parent, $closure) {
            $closure = $closure->bindTo($this);

            return $closure($parent());
        };

        $this->services[$name]->setClosure($extend);

        return $this;
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return TaggedServices
     */
    public function getTaggedServices($name)
    {
        if (!isset($this->tags[$name])) {
            throw new \InvalidArgumentException('Undefined tag: ' . $name);
        }

        return $this->tags[$name];
    }

    /**
     * @param string  $name
     * @param Service $service
     * @param array   $tags
     */
    private function register($name, Service $service, $tags = [])
    {
        $this->services[$name] = $service;

        foreach ($tags as $tag) {
            if (!isset($this->tags[$tag])) {
                $this->tags[$tag] = new TaggedServices();
            }

            $this->tags[$tag]->add($service);
        }
    }

} 