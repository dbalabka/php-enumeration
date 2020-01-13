<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Enum;

use Dbalabka\Enumeration\Enumeration;

/**
 * Rust Option implementation
 *
 * @author Dmitry Balabka <dmitry.balabka@gmail.com>
 * @template T
 */
class Option extends Enumeration
{
    /**
     * @psalm-var Option<mixed>
     */
    public static $some;

    /**
     * @psalm-var Option<null>
     */
    public static $none;

    /**
     * @psalm-var T
     */
    private $value;

    /**
     * @psalm-param T $value
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    protected static function initializeValues(): void
    {
        self::$some = new class (null) extends Option { };
        self::$none = new class (null) extends Option { };
    }

    /**
     * @psalm-return T
     */
    public function unwrap()
    {
        if ($this->isSome()) {
            return $this->value;
        }
        throw new \Exception('Called `Option::unwrap()` on a `Option::$none` value');
    }

    /**
     * @psalm-param T $default
     * @psalm-return T
     */
    public function unwrapOr($default)
    {
        return $this->isSome() ? $this->value : $default;
    }

    /**
     * @psalm-param callable():T $func
     * @psalm-return T
     */
    public function unwrapOrElse(callable $func)
    {
        if ($this->isSome()) {
            return $this->value;
        }
        return $func();
    }

    public function isSome():bool
    {
        return $this instanceof Option::$some;
    }

    public function isNone():bool
    {
        return !$this->isSome();
    }

    /**
     * @psalm-param T $x
     */
    public function contains($x): bool
    {
        if ($this->isSome()) {
            return $this->value === $x;
        }
        return false;
    }

    /**
     * @psalm-return T
     */
    public function expect(string $message)
    {
        if ($this->isSome()) {
            return $this->value;
        }
        throw new \Exception($message);
    }

    /**
     * @template G
     * @psalm-param G $value
     * @psalm-return Option<G>
     */
    public function __invoke($value)
    {
        if ($this instanceof Option::$none) {
            throw new \Exception('Can not instantiate option of none');
        }
        return new static($value);
    }
}
