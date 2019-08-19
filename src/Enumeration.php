<?php
declare(strict_types=1);

namespace Dbalabka;

use ArrayAccessible;
use Dbalabka\Exception\InitializationException;
use Exception;
use function get_class;
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
abstract class Enumeration
{
    const INITIAL_ORDINAL = 0;

    /**
     * @var int
     */
    protected $ordinal;

    private static $initializedEnums = [];

    /**
     * This method should be called right after enumerate class declaration.
     * Unfortunately, PHP does not support static initialization.
     * See static init RFC: https://wiki.php.net/rfc/static_class_constructor
     * Typed Properties will help to control of calling this method.
     */
    final protected static function __constructStatic() : void
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
        self::$initializedEnums[static::class] = true;
        static::initializeValues();
        static::initializeOrdinals();
    }

    protected static function initializeOrdinals() : void
    {
        $ordinal = static::INITIAL_ORDINAL;
        foreach (static::values() as $value) {
            $value->ordinal = $ordinal++;
        }
    }

    protected static function initializeValues() : void
    {
        $firstEnumItem = new static();

        $staticVars = static::getStaticVars($firstEnumItem);

        $firstEnumName = key($staticVars);
        static::$$firstEnumName = $firstEnumItem;
        array_shift($staticVars);

        foreach ($staticVars as $name => $value) {
            static::$$name = new static();
        }
    }

    private static function getStaticVars(Enumeration $enum): array
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
    public static function values() : array
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
    public static function valueOf(string $name)
    {
        if ($value = static::values()[$name] ?? null) {
            return $value;
        }
        throw new InvalidArgumentException(sprintf('Invalid "%s" enum item name', $name));
    }

    protected function __construct()
    {
    }

    public function ordinal() : int
    {
        if (null === $this->ordinal) {
            // When we call ordinal() in constructor the ordinal isn't initialized yet.
            // It is the only one case when ordinal isn't initialized.
            $staticVars = static::getStaticVars($this);
            $ordinal = static::INITIAL_ORDINAL;
            foreach ($staticVars as $var) {
                if ($var === null || $var === $this) {
                    $this->ordinal = $ordinal;
                    break;
                }
                $ordinal++;
            }
        }
        return $this->ordinal;
    }

    public function compareTo(self $enum) : int
    {
        return $this->ordinal() - $enum->ordinal();
    }

    public function __toString() : string
    {
        return $this->name();
    }

    public function name() : string
    {
        return \array_search($this, static::values(), true);
    }

    final public function __clone()
    {
        throw new EnumerationException('Enum cloning is not allowed');
    }

    /**
     * Serialization is not allowed right now. It is not possible to properly serialize the singleton.
     */
    final public function __sleep()
    {
        throw new EnumerationException('Enum serialization is not allowed');
    }

    final public function __wakeup()
    {
        throw new EnumerationException('Enum unserialization is not allowed');
    }
}
