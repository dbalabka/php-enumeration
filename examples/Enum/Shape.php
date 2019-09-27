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
    public static $circle;
    public static $rectangle;

    protected static function initializeValues(): void
    {
        self::$circle = new class extends Shape
        {
            private Point $point;
            private float $radius;

            public function __invoke(Point $point, float $radius)
            {
                $circle = new static();
                $circle->point = $point;
                $circle->radius = $radius;
                return $circle;
            }
        };

        self::$rectangle = new class extends Shape
        {
            private Point $pointA;
            private Point $pointB;

            public function __invoke(Point $pointA, Point $pointB)
            {
                $rectangle = new static();
                $rectangle->pointA = $pointA;
                $rectangle->pointB = $pointB;
                return $rectangle;
            }
        };
    }
}
