<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Enum;

use Dbalabka\Enumeration\Examples\Struct\Point;

final class Rectangle extends Shape
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
}
