# PhpPublisher

[![Build Status](https://travis-ci.com/hakito/PhpPublisher.svg?branch=master)](https://travis-ci.com/hakito/PhpPublisher)
[![Coverage Status](https://coveralls.io/repos/github/hakito/PhpPublisher/badge.svg?branch=master)](https://coveralls.io/github/hakito/PhpPublisher?branch=master)
[![Latest Stable Version](https://poser.pugx.org/hakito/publisher/v/stable)](https://packagist.org/packages/hakito/publisher)
[![Total Downloads](https://poser.pugx.org/hakito/publisher/downloads)](https://packagist.org/packages/hakito/publisher)
[![Latest Unstable Version](https://poser.pugx.org/hakito/publisher/v/unstable)](https://packagist.org/packages/hakito/publisher)
[![License](https://poser.pugx.org/hakito/publisher/license)](https://packagist.org/packages/hakito/publisher)

Simple proxy for accessing protected/private class members.

It's intention is to give unit tests access to private members without
reinventing the wheel.

This helper uses closures
to access private members of a class instead of reflection.

## Installation

```bash
composer require hakito/publisher
```

## Usage

Guess you have a class with private members:

```php
class Target
{
    private $foo = 'secret';
    private function bar($arg) { return $arg . 'Bar'; }

    private static $sFoo = 'staticSecret';
}
```

Create the proxy to access these members:

```php
$target = new Target();
$published = new hakito\Publisher\Published($target);

// Get private property
$property = $published->foo;
// $property = 'secret';

// Set private property
$published->foo = 'outsider';
// $target->foo = 'outsider';

// call private method
$name = $published->bar('Saloon');
// $name = 'SaloonBar';


// Optional you can provide a base class in the constructor
class Derived extends Target {
    private $foo = 'derived';
}

$derived = new Derived();
$published = new hakito\Publisher\Published($derived, Target::class);
$property = $published->foo; // Gets property from Target
// $property = 'secret';
```

### Accessing static members

If you want to access static fields or methods you have to use the class StaticPublished

```php
$published = new StaticPublished(Target::class);

$property = $published->sFoo;
// $property = 'staticSecret'
```

Setting fields and calling methods works the same as for instances.

## Limitations

### The published method call cannot set a reference argument.

```php
class Target
{
    private function methodWithReferenceArgument(&$arg) { $arg = 'hi'; }
}

$target = new Target();
$published = new hakito\Publisher\Published($target);
$val = 'initial';
$published->methodWithReferenceArgument($val);
// $val is still 'initial'
```

### The return value from method call cannot be a reference

```php
class Target
{
    private $_member = [];
    private function &methodWithReferenceReturnValue($arg) { return $this->_member; }
}

$target = new Target();
$published = new hakito\Publisher\Published($target);
$val = 'initial';
$published->methodWithReferenceReturnValue($val)['new'] = 'value';
// Target::_member is still []
```
