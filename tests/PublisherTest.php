<?php

namespace hakito\Publisher\Test;

use PHPUnit\Framework\TestCase;

use hakito\Publisher\Published;

class TargetBase
{
    protected $p_foo = 'base';
    private $foo = 'baseFoo';
}

class Target extends TargetBase
{
    private $foo = 'initial';
    private function bar($arg) { return $arg . 'Bar'; }
    private $propertyGetter = 'something';

    public function getFoo() { return $this->foo; }
    public function getHidden() { return $this->propertyGetter; }
}

final class PublisherTest extends TestCase
{

    public function setUp() : void
    {
        parent::setUp();
        $this->target = new Target();
        $this->published = new Published($this->target);
    }

    public function testGetProperty()
    {
        $this->assertEquals('initial', $this->published->foo);
    }

    public function testPublishBaseClass()
    {
        $p = new Published($this->target, TargetBase::class);
        $this->assertEquals('baseFoo', $p->foo);
    }

    public function testSetProperty()
    {
        $this->published->foo = 'overwritten';
        $this->assertEquals('overwritten', $this->published->foo);
        $this->assertEquals('overwritten', $this->target->getFoo());
    }

    public function testProxyPropertiesAreHidden()
    {
        $this->assertEquals('something', $this->published->propertyGetter);
        $this->published->propertyGetter = 'else';
        $this->assertEquals('else', $this->published->propertyGetter);
        $this->assertEquals('else', $this->target->getHidden());
    }

    public function testGetBaseProperty()
    {
        $this->assertEquals('base', $this->published->p_foo);
    }

    public function testCallFunction()
    {
        $this->assertEquals('FunkyBar', $this->published->bar('Funky'));
    }
}