<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Enum;

use Dbalabka\Enumeration\Enumeration;
use Dbalabka\Enumeration\Examples\Struct\Point;

/**
 * Class Shape
 *
 * @author Dmitry Balabka <dmitry.balabka@gmail.com>
 */
abstract class Shape extends Enumeration
{
    /** @var Circle */
    public static $circle;
    /** @var Rectangle */
    public static $rectangle;

    protected static function initializeValues(): void
    {
        self::$circle = new Circle();
        self::$rectangle = new Rectangle();
    }
}
