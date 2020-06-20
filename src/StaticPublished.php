<?php

namespace hakito\Publisher;

class StaticPublished
{
    private $propertyGetter;
    private $propertySetter;
    private $functionCaller;

    public function __construct($class)
    {
        $this->propertyGetter = \Closure::bind(function&($property) use ($class) { return $class::$$property; }, null, $class) ;
        $this->propertySetter = \Closure::bind(function($property, $value) use ($class) { return $class::$$property = $value; }, null, $class) ;
        $this->functionCaller = \Closure::bind(function($name, array $arguments) use ($class) { return call_user_func_array([$class, $name], $arguments); }, null, $class);
    }

    public function &__get($property)
    {
        return ($this->propertyGetter)($property);
    }

    public function __set($property, $value)
    {
        ($this->propertySetter)($property, $value);
    }

    public function __call($name, array $arguments)
    {
        return ($this->functionCaller)($name, $arguments);
    }
}