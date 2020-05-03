<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Enum;

use Dbalabka\Enumeration\Enumeration;

final class Color extends Enumeration
{
    /** @var self */
    public static $red;
    /** @var self */
    public static $green;
    /** @var self */
    public static $blue;

    private $value;

    protected function __construct(int $value)
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
