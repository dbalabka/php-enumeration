<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Enum;

use Dbalabka\Enumeration\Examples\Struct\Point;

class Circle extends Shape
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
}
