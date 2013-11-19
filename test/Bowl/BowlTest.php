<?php

class BowlTest extends \PHPUnit_Framework_TestCase
{

    public function testParameters()
    {
        $bowl = new \Bowl\Bowl();
        $bowl['foo'] = 'bar';

        $this->assertEquals('bar', $bowl['foo']);

        unset($bowl['foo']);

        $this->assertFalse(isset($bowl['foo']));
    }

    public function testSingleton()
    {
        $bowl = new \Bowl\Bowl();
        $bowl->share('test', function () {
            return new stdClass();
        });

        $this->assertNotSame(new stdClass(), new stdClass());
        $this->assertSame($bowl->get('test'), $bowl->get('test'));
    }

    public function testFactory()
    {
        $bowl = new \Bowl\Bowl();
        $bowl->factory('test', function () {
            return new stdClass();
        });

        $this->assertNotSame($bowl->get('test'), $bowl->get('test'));
    }

    public function testDependencies()
    {
        $bowl = new \Bowl\Bowl();
        $bowl->factory('child', function () {
            $object = new stdClass();
            $object->name = 'foo';

            return $object;
        });
        $bowl->share('parent', function () {
            $object = new stdClass();
            $object->child = $this->get('child');

            return $object;
        });

        $object = $bowl->get('parent');

        $this->assertEquals('foo', $object->child->name);
    }

    public function testTaggedServices()
    {
        $bowl = new \Bowl\Bowl();
        $bowl->share('taggedA', function () {
            $object = new stdClass();
            $object->name = 'A';

            return $object;
        }, ['tag']);
        $bowl->share('taggedB', function () {
            $object = new stdClass();
            $object->name = 'B';

            return $object;
        }, ['tag']);

        $this->assertCount(2, $bowl->getTaggedServices('tag'));
        $this->assertEquals([$bowl->get('taggedA'), $bowl->get('taggedB')], iterator_to_array($bowl->getTaggedServices('tag')));
    }

    public function testExtend()
    {
        $bowl = new \Bowl\Bowl();
        $bowl->share('test', function () {
            return new stdClass();
        });
        $bowl->extend('test', function ($object) {
            $object->name = 'foo';

            return $object;
        });

        $object = $bowl->get('test');
        $this->assertEquals('foo', $object->name);
    }

    public function testResetService()
    {
        $bowl = new \Bowl\Bowl();
        $bowl->share('test', function () {
            return new stdClass();
        });

        $objA = $bowl->get('test');
        $objB = $bowl->get('test');
        $objC = $bowl->reset('test')->get('test');

        $this->assertSame($objA, $objB);
        $this->assertNotSame($objA, $objC);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidServiceName()
    {
        $bowl = new \Bowl\Bowl();
        $bowl->get('foo.bar');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidServiceNameOnExtend()
    {
        $bowl = new \Bowl\Bowl();
        $bowl->extend('foo.bar', function () {});
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidTag()
    {
        $bowl = new \Bowl\Bowl();
        $bowl->getTaggedServices('foo.bar');
    }
} 