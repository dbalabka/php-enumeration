<?php
declare(strict_types=1);

namespace Dbalabka\Examples\Enum;

use Dbalabka\Enumeration;

final class Planet extends Enumeration
{
    public static Planet $mercury;
    public static Planet $venus;
    public static Planet $earth;
    public static Planet $mars;
    public static Planet $jupiter;
    public static Planet $saturn;
    public static Planet $uranus;
    public static Planet $neptune;

    private float $mass;   // in kilograms
    private float $radius; // in meters

    // universal gravitational constant  (m3 kg-1 s-2)
    private const G = 6.67300E-11;

    protected function __construct(float $mass, float $radius)
    {
        $this->mass = $mass;
        $this->radius = $radius;
    }

    protected static function initializeValues() : void
    {
        self::$mercury = new self(3.303e+23, 2.4397e6);
        self::$venus   = new self(4.869e+24, 6.0518e6);
        self::$earth   = new self(5.976e+24, 6.37814e6);
        self::$mars    = new self(6.421e+23, 3.3972e6);
        self::$jupiter = new self(1.9e+27,   7.1492e7);
        self::$saturn  = new self(5.688e+26, 6.0268e7);
        self::$uranus  = new self(8.686e+25, 2.5559e7);
        self::$neptune = new self(1.024e+26, 2.4746e7);
    }

    public function surfaceGravity() : float
    {
        return self::G * $this->mass / ($this->radius * $this->radius);
    }

    public function surfaceWeight(float $otherMass) {
        return $otherMass * $this->surfaceGravity();
    }
}
