<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration;

use Dbalabka\Enumeration\Exception\EnumerationException;
use Dbalabka\Enumeration\Exception\InvalidArgumentException;
use Dbalabka\StaticConstructorLoader\StaticConstructorInterface;
use ReflectionClass;
use Serializable;
use function array_search;
use function get_class_vars;
use function sprintf;

/**
 * Inspired by https://docs.microsoft.com/en-us/dotnet/architecture/microservices/microservice-ddd-cqrs-patterns/enumeration-classes-over-enum-types
 * Implemented using https://github.com/dotnet-architecture/eShopOnContainers/blob/8960db40d43d79ad475799dedfe311ebc49cab99/src/Services/Ordering/Ordering.Domain/SeedWork/Enumeration.cs
 * Enumerated types RFC: https://wiki.php.net/rfc/enum
 * Each static property should be declared as read-only, unfortunately, PHP does not support this (see RFC https://wiki.php.net/rfc/readonly_properties)
 *
 * @author Dmitry Balabka <dmitry.balabka@gmail.com>
 */
abstract class Enumeration implements StaticConstructorInterface, Serializable
{
    const INITIAL_ORDINAL = 0;

    /**
     * @var int|null
     */
    protected $ordinal;

    /** @var array */
    private static $initializedEnums = [];

    /**
     * @throws EnumerationException
     */
    final public static function __constructStatic() : void
    {
        if (self::class === static::class) {
            return;
        }
        static::initialize();
    }

    final public static function initialize(): void
    {
        if (isset(self::$initializedEnums[static::class])) {
            return;
        }

        /**
         * Using reflections we can enforce the strict rules of enums declaration.
         * It should not rise performance issue because enumeration class initialization executes only once.
         */
        $classReflection = new ReflectionClass(static::class);
        if (!$classReflection->isAbstract() && !$classReflection->isFinal()) {
            throw new EnumerationException('Enumeration class should be declared as final');
        }
        $method = $classReflection->getConstructor();
        if ($method && $method->isPublic()) {
            throw new EnumerationException('Enumeration class constructor should not be public');
        }

        static::initializeValues();
        static::initializeOrdinals();
        self::$initializedEnums[static::class] = true;
    }

    final protected static function initializeOrdinals() : void
    {
        $ordinal = static::INITIAL_ORDINAL;
        foreach (static::values() as $value) {
            $value->ordinal = $ordinal++;
        }
    }

    /**
     * Override this method to manually initialize Enum values. Useful when __construct() accepts at least one argument.
     * Enum objects does not have any properties by default.
     */
    protected static function initializeValues() : void
    {
        /**
         * TODO: Unfortunately, it is impossible to avoid class reflection here.
         *       The reflection caching here should not give major performance benefit.
         *       It should be properly benchmarked.
         */
        if ((new ReflectionClass(static::class))->isAbstract()) {
            return;
        }
        $firstEnumItem = new static();

        $staticVars = static::getEnumStaticVars($firstEnumItem);

        $firstEnumName = key($staticVars);
        static::$$firstEnumName = $firstEnumItem;
        array_shift($staticVars);

        foreach ($staticVars as $name => $value) {
            static::$$name = new static();
        }
    }

    final protected static function getEnumStaticVars(Enumeration $enum): array
    {
        $nonStaticVars = get_object_vars($enum);
        $allVars = get_class_vars(static::class);
        $staticVars = array_diff_key($allVars, $nonStaticVars);
        unset($staticVars['initializedEnums']);
        return $staticVars;
    }

    /**
     * @return static[]
     */
    final public static function values() : array
    {
        return array_filter(
            get_class_vars(static::class),
            static function ($value) {
                return $value instanceof Enumeration;
            }
        );
    }

    /**
     * @return static
     */
    final public static function valueOf(string $name)
    {
        if ($value = static::values()[$name] ?? null) {
            return $value;
        }
        throw new InvalidArgumentException(sprintf('Invalid "%s" enum item name', $name));
    }

    /**
     * Override default constructor to set object properties for each Enum value.
     */
    protected function __construct()
    {
    }

    /**
     * @throws EnumerationException
     */
    final public function ordinal() : int
    {
        if (null === $this->ordinal) {
            // When we call ordinal() in constructor the ordinal isn't initialized yet.
            // It is the only one case when ordinal isn't initialized.
            $staticVars = static::getEnumStaticVars($this);
            $ordinal = static::INITIAL_ORDINAL;
            foreach ($staticVars as $var) {
                if ($var === null || $var === $this) {
                    $this->ordinal = $ordinal;
                    break;
                }
                $ordinal++;
            }
        }
        if ($this->ordinal === null) {
            throw new EnumerationException('Ordinal initialization failed. Enum does not contain any static variables');
        }
        return $this->ordinal;
    }

    /**
     * @throws EnumerationException
     */
    final public function compareTo(self $enum) : int
    {
        return $this->ordinal() - $enum->ordinal();
    }

    /**
     * Override this method to custom "programmer-friendly" string representation of Enum value.
     * It returns Enum name by default.
     */
    public function __toString() : string
    {
        return $this->name();
    }

    final public function name() : string
    {
        $name = array_search($this, static::values(), true);
        if (false === $name) {
            throw new EnumerationException(
                sprintf(
                    'Can not find $this in %s::values(). ' .
                    'It seems that the static property was overwritten. This is not allowed.',
                    get_class($this)
                )
            );
        }
        return $name;
    }

    final public function __clone()
    {
        throw new EnumerationException('Enum cloning is not allowed');
    }

    /**
     * Serialization is not allowed right now. It is not possible to properly serialize the singleton.
     * See the documentation for workaround.
     */
    final public function __serialize()
    {
        throw new EnumerationException('Enum serialization is not allowed');
    }

    final public function __unserialize()
    {
        throw new EnumerationException('Enum unserialization is not allowed');
    }

    final public function serialize()
    {
        throw new EnumerationException('Enum serialization is not allowed');
    }

    final public function unserialize($data)
    {
        throw new EnumerationException('Enum unserialization is not allowed');
    }
}
