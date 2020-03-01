# PhpPublisher

Simple proxy for accessing protected/private class members.

It's intention is to give unit tests access to private members without
reinventing the wheel.

This helper uses closures
to access private members of a class instead of reflection.

## Installation

```bash
composer require hakito/Publisher
```

## Usage

Guess you have a class with private members:

```php
class Target
{
    private $foo = 'secret';
    private function bar($arg) { return $arg . 'Bar'; }
}
```

Create the proxy to access these members:

```php
$target = new Target();
$published = new hakito/Publisher/Published($target);

// Get private property
$property = $published->foo;
// $property = 'secret';

// Set private property
$published->foo = 'outsider';
// $target->foo = 'outsider';

// call private method
$name = $published->bar('Saloon');
// $name = 'SaloonBar';
```

## Limitations

The published method call cannot set a reference argument.

```php
class Target
{
    private function methodWithReferenceArgument(&$arg) { $arg = 'hi'; }
}

$target = new Target();
$published = new hakito/Publisher/Published($target);
$val = 'initial';
$published->methodWithReferenceArgument($val);
// $val is still 'initial'
```
