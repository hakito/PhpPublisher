<?php

namespace hakito\Publisher;

class Published
{
    private $propertyGetter;
    private $propertySetter;
    private $functionCaller;

    public function __construct($target, $class = null)
    {
        if ($class == null)
            $class = get_class($target);

        $this->propertyGetter = \Closure::bind(function($property) { return $this->{$property}; }, $target, $class) ;
        $this->propertySetter = \Closure::bind(function($property, $value) { return $this->{$property} = $value; }, $target, $class) ;
        $this->functionCaller = \Closure::bind(function($name, array $arguments) { return call_user_func_array([$this, $name], $arguments); }, $target, $class);
    }

    public function __get($property)
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