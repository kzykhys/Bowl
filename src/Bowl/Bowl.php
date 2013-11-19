<?php

namespace Bowl;

use Bowl\Service\FactoryService;
use Bowl\Service\Service;
use Bowl\Service\SharedService;
use Bowl\Traits\Parameters;
use Traversable;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Bowl implements \ArrayAccess, \IteratorAggregate
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
     * @var Bowl[]
     */
    private $environments = [];

    /**
     * @var string
     */
    private $env = null;

    /**
     * Constructor
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;
    }

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
     * @param          $environment
     * @param callable $closure
     *
     * @return $this
     */
    public function configure($environment, \Closure $closure)
    {
        if (!isset($this->environments[$environment])) {
            $this->environments[$environment] = new static();
        }

        $closure($this->environments[$environment]);

        return $this;
    }

    /**
     * @param $environment
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function env($environment) {
        if (!isset($this->environments[$environment])) {
            throw new \InvalidArgumentException('Undefined environment: ' . $environment);
        }

        if ($this->env && $this->env != $environment) {
            throw new \LogicException('You can\'t switch environment. (Current: ' . $this->env . ')');
        }

        $this->env = $environment;
        $this->merge($this->environments[$environment]);
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b> or <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->parameters);
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

    /**
     * @param Bowl $bowl
     */
    private function merge(Bowl $bowl)
    {
        foreach ($bowl->parameters as $key => $value) {
            $this[$key] = $value;
        }

        foreach ($bowl->services as $key => $value) {
            $this->services[$key] = $value;
        }

        foreach ($bowl->tags as $key => $value) {
            if (!isset($this->tags[$key])) {
                $this->tags[$key] = new TaggedServices();
            }

            foreach ($value->getServices() as $service) {
                $this->tags[$key]->add($service);
            }
        }
    }

}