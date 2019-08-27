<?php
declare(strict_types=1);

namespace Dbalabka\Examples\Enum;

use Dbalabka\Enumeration;

final class Color extends Enumeration
{
    public static $red;
    public static $green;
    public static $blue;

    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    protected static function initializeValues(): void
    {
        self::$red = new self(1);
        self::$blue = new self(2);
        self::$green = new self(3);
    }
}
