# PHP Enumeration classes
[![Build Status](https://travis-ci.org/dbalabka/php-enumeration.svg?branch=master)](https://travis-ci.org/dbalabka/php-enumeration)
[![Coverage Status](https://coveralls.io/repos/github/dbalabka/php-enumeration/badge.svg)](https://coveralls.io/github/dbalabka/php-enumeration)

Implementation of [Enumeration Classes](https://docs.microsoft.com/en-us/dotnet/architecture/microservices/microservice-ddd-cqrs-patterns/enumeration-classes-over-enum-types) in PHP.
The better alternative for Enums.

In contrast to [existing solutions](#existing-solutions), this implementation avoids usage of [Magic methods](https://www.php.net/manual/en/language.oop5.magic.php) and 
[Reflection](https://www.php.net/manual/en/book.reflection.php) to provide better performance and code autocompletion.
The Enumeration class holds a reference to a single Enum element represented as an object (singleton) to provide the possiblity 
to use strict (`===`) comparision between the values.
Also, it uses static properties that can utilize the power of [Typed Properties](https://wiki.php.net/rfc/typed_properties_v2).
The Enumeration Classes are much closer to other language implementations like [Java Enums](https://docs.oracle.com/javase/tutorial/java/javaOO/enum.html) 
and [Python Enums](https://docs.python.org/3/library/enum.html).
 

## Declaration

A basic way to declare a named Enumeration class:
```php
<?php
use Dbalabka\Enumeration\Enumeration;

final class Action extends Enumeration
{
    public static $view;
    public static $edit;
}
Action::initialize();
```
**Note!** You should always call the `Enumeration::initialize()` method right after Enumeration Class declaration.
To avoid manual initialization you can setup the [StaticConstructorLoader](#class-static-initialization) provided in this library.

Declaration with Typed Properties support:
```php
<?php
final class Day extends Enumeration
{
    public static Day $sunday;
    public static Day $monday;
    public static Day $tuesday;
    public static Day $wednesday;
    public static Day $thursday;
    public static Day $friday;
    public static Day $saturday; 
}
Day::initialize();
```

By default an enumeration class does not require the value to be provided. You can use the constructor to set any types of values.
1. Flag enum implementation example: 
    ```php
    <?php
    final class Flag extends Enumeration
    {
        public static Flag $ok;
        public static Flag $notOk;
        public static Flag $unavailable;
    
        private int $flagValue;
    
        protected function __construct()
        {
            $this->flagValue = 1 << $this->ordinal();
        }
    
        public function getFlagValue() : int
        {
            return $this->flagValue;
        }
    }
    ```
2. You should override `initializeValues()` method to set custom values for each Enum element.
    ```php
    <?php
    
    final class Planet extends Enumeration
    {
        public static Planet $mercury;
        public static Planet $venus;
        public static Planet $earth;
        public static Planet $mars;
        public static Planet $jupiter;
        public static Planet $saturn;
        public static Planet $uranus;
        public static Planet $neptune;
    
        private float $mass;   // in kilograms
        private float $radius; // in meters
    
        // universal gravitational constant  (m3 kg-1 s-2)
        private const G = 6.67300E-11;
    
        protected function __construct(float $mass, float $radius)
        {
            $this->mass = $mass;
            $this->radius = $radius;
        }
        
        protected static function initializeValues() : void
        {
            self::$mercury = new self(3.303e+23, 2.4397e6);
            self::$venus   = new self(4.869e+24, 6.0518e6);
            self::$earth   = new self(5.976e+24, 6.37814e6);
            self::$mars    = new self(6.421e+23, 3.3972e6);
            self::$jupiter = new self(1.9e+27,   7.1492e7);
            self::$saturn  = new self(5.688e+26, 6.0268e7);
            self::$uranus  = new self(8.686e+25, 2.5559e7);
            self::$neptune = new self(1.024e+26, 2.4746e7);
        }
    
        public function surfaceGravity() : float 
        {
            return self::G * $this->mass / ($this->radius * $this->radius);
        }
    
        public function surfaceWeight(float $otherMass) {
            return $otherMass * $this->surfaceGravity();
        }
    }
    ```
 
Declaration rules that the developer should follow:
1. The resulting Enumeration class should be marked as `final`. Abstract classes should be used to share functionality between
multiple Enumeration classes.
   > ...Allowing subclassing of enums that define members would lead to a violation of some important invariants of types and instances. 
   > On the other hand, it makes sense to allow sharing some common behavior between a group of enumerations...
   > (from [Python Enum documentation](https://docs.python.org/3/library/enum.html#restricted-enum-subclassing))
2. Constructor should always be declared as non-public (`private` or `protected`) to avoid unwanted class instantiation.
3. Implementation is based on assumption that all class static properties are elements of Enum. <!-- If there is a need to declare
any static property that isn't an Enum element then you should override the `\Dbalabka\Enumeration\Enumeration::$notEnumVars` method. -->
4. The method `Dbalabka\Enumeration\Enumeration::initialize()` should be called after each Enumeration class declaration. Please use the 
[StaticConstructorLoader](#class-static-initialization) provided in this library to avoid boilerplate code.

## Usage
```php
<?php
use Dbalabka\Enumeration\Examples\Enum\Action;

$viewAction = Action::$view;

// it is possible to compare Enum elements
assert($viewAction === Action::$view);

// you can get Enum element by name 
$editAction = Action::valueOf('edit');
assert($editAction === Action::$edit);

// iterate over all Enum elements
foreach (Action::values() as $name => $action) {
    assert($action instanceof Action);
    assert($name === (string) $action);
    assert($name === $action->name());
}
```

## Known issues
### Readonly Properties
In the current implementation, static property value can be occasionally replaced. 
[Readonly Properties](https://wiki.php.net/rfc/readonly_properties) is aimed to solve this issue.
In an ideal world Enum values should be declared as a constants. Unfortunately, it is not possible in PHP right now.
```php
<?php
// It is possible but don't do it
Action::$view = Action::$edit;
// Following isn't possible in PHP 7.4 with declared properties types
Action::$view = null;
```

### Class static initialization 
This implementation relies on class static initialization which was proposed in [Static Class Constructor](https://wiki.php.net/rfc/static_class_constructor).
The RFC is still in Draft status but it describes possible workarounds. The simplest way is to call the initialization method right after the class declaration, 
but it requires the developer to keep this in mind. Thanks to [Typed Properties](https://wiki.php.net/rfc/typed_properties_v2)
we can control uninitialized properties - PHP will throw an error in case of access to an uninitialized property.
It might be automated with custom autoloader [Dbalabka\StaticConstructorLoader\StaticConstructorLoader](./src/StaticConstructorLoader/StaticConstructorLoader.php) 
provided in this library:
```php
<?php 
use Dbalabka\StaticConstructorLoader\StaticConstructorLoader;

$composer = require_once(__DIR__ . '/vendor/autoload.php');
$loader = new StaticConstructorLoader($composer);
``` 
See [examples/class_static_construct.php](examples/class_static_construct.php) for example. 

### Serialization
There is no possibility to serialize the singleton. As a result, we have to restrict direct Enum object serialization.
```php
<?php
// The following line will throw an exception
serialize(Action::$view);
```
[New custom object serialization mechanism](https://wiki.php.net/rfc/custom_object_serialization) does not help with singleton serialization
but it gives the possibility to control this in the class which holds the references to Enum instances. Also, it can be worked around
with [Serializable Interface](https://www.php.net/manual/en/class.serializable.php) in a similar way.
For example, Java [handles](https://stackoverflow.com/questions/15521309/is-custom-enum-serializable-too/15522276#15522276) Enums serialization differently than other classes, but you can serialize it directly thanks to [readResolve()](https://docs.oracle.com/javase/7/docs/api/java/io/Serializable.html).
In PHP, we can't serialize Enums directly, but we can handle Enums serialization in class that holds the reference. We can serialize the name of the Enum constant and use `valueOf()` method to obtain the Enum constant value during unserialization. 
So this problem somewhat resolved a the cost of a worse developer experience. Hope it will be solved in future RFCs.
```php
class SomeClass
{
    public Action $action;

    public function __serialize()
    {
        return ['action' => $this->action->name()];
    }

    public function __unserialize($payload)
    {
        $this->action = Action::valueOf($payload['action']);
    }
}
``` 
See complete example in [examples/serialization_php74.php](examples/serialization_php74.php).  

## Existing solutions
* https://github.com/myclabs/php-enum
* https://github.com/marc-mabe/php-enum
* https://www.php.net/manual/en/class.splenum.php
* [PHP RFC: Enumerated Types](https://wiki.php.net/rfc/enum)

(there are a lot of [other PHP implementations](https://packagist.org/search/?query=php-enum))

## References
* [Enum Types](https://docs.oracle.com/javase/tutorial/java/javaOO/enum.html) - The Java™ Tutorials
* [Enum — Support for enumerations](https://docs.python.org/3/library/enum.html) - The Python Standard Library
    * [Class Enum<E extends Enum<E>>](https://docs.oracle.com/javase/7/docs/api/java/lang/Enum.html) - Java™ Platform, Standard Edition 7 API Specification
* [Use enumeration classes instead of enum types](https://docs.microsoft.com/en-us/dotnet/architecture/microservices/microservice-ddd-cqrs-patterns/enumeration-classes-over-enum-types) - .NET microservices - Architecture e-book
    * [Enumeration Class implementation](https://github.com/dotnet-architecture/eShopOnContainers/blob/8960db40d43d79ad475799dedfe311ebc49cab99/src/Services/Ordering/Ordering.Domain/SeedWork/Enumeration.cs) - Microservices Architecture and Containers based Reference Application
* [Python Enum is a singleton](https://repl.it/@torinaki/Python-Enum-is-Singleton)
* [Java Enum is a singleton](https://repl.it/@torinaki/Java-Enum-is-Singleton)
