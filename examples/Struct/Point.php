<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Struct;

class Point
{
    protected float $x;
    protected float $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}
