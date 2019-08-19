<?php
declare(strict_types=1);

use Dbalabka\Enumeration;

require_once(__DIR__ . '/../vendor/autoload.php');

final class Color extends Enumeration
{
    public static Color $red;
    public static Color $green;
    public static Color $blue;

    private int $value;

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
Color::initialize();

class Square implements Serializable
{
    public Color $color;

    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    public function serialize()
    {
        return serialize([$this->color->name()]);
    }

    public function unserialize($serialized)
    {
        [$color] = unserialize($serialized);
        $this->color = Color::valueOf($color);
    }
}
$square = new Square(Color::$red);

$red = Color::$red;
try {
    $serialized = serialize($red);
} catch (\Dbalabka\EnumerationException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}


$serializedSquare = serialize($square);
$square = unserialize($serializedSquare);

var_dump($square->color === Color::$red);


class Dot
{
    public Color $color;

    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    public function __serialize()
    {
        return ['color' => $this->color->name()];
    }

    public function __unserialize($payload)
    {
        $this->color = Color::valueOf($payload['color']);
    }
}
$dot = new Dot(Color::$red);

$serializedDot = serialize($dot);
$dot = unserialize($serializedDot);

var_dump($dot->color === Color::$red);
