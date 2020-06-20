<?php

namespace hakito\Publisher\Test;

use PHPUnit\Framework\TestCase;

use hakito\Publisher\StaticPublished;

class StaticTargetBase
{
    protected static $p_foo = 'base';
    private static $foo = 'baseFoo';
}

class StaticTarget extends StaticTargetBase
{
    private static $arr = [];
    private static $foo = 'initial';
    private static function bar($arg) { return $arg . 'Bar'; }
    private static $propertyGetter = 'something';

    public static function getArr() { return self::$arr; }
    public static function getFoo() { return self::$foo; }
    public static function getHidden() { return self::$propertyGetter; }
}

final class StaticPublishedTest extends TestCase
{

    public function setUp() : void
    {
        parent::setUp();
        $this->published = new StaticPublished(StaticTarget::class);
    }

    public function testGetProperty()
    {
        $this->assertEquals('initial', $this->published->foo);
    }

    public function testGetPropertyReference()
    {
        $this->published->arr['IsSet'] = 'ByRef';
        $this->assertEquals(['IsSet' => 'ByRef'], StaticTarget::getArr());
    }

    public function testPublishBaseClass()
    {
        $p = new StaticPublished(StaticTargetBase::class);
        $this->assertEquals('baseFoo', $p->foo);
    }

    public function testSetProperty()
    {
        $this->published->foo = 'overwritten';
        $this->assertEquals('overwritten', $this->published->foo);
        $this->assertEquals('overwritten', StaticTarget::getFoo());
    }

    public function testProxyPropertiesAreHidden()
    {
        $this->assertEquals('something', $this->published->propertyGetter);
        $this->published->propertyGetter = 'else';
        $this->assertEquals('else', $this->published->propertyGetter);
        $this->assertEquals('else', StaticTarget::getHidden());
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