<?php
declare(strict_types=1);

use Dbalabka\EnumerationException;
use Dbalabka\Examples\Enum\Color;

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    throw new \Exception('This code requires PHP >= 7.4');
}

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new ConstructStatic\Loader($composer);

class Square implements Serializable
{
    public $color;

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
} catch (EnumerationException $e) {
    assert($e->getMessage() === 'Enum serialization is not allowed');
}


$serializedSquare = serialize($square);
$square = unserialize($serializedSquare);

assert($square->color === Color::$red);


class Dot
{
    public $color;

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

assert($dot->color === Color::$red);
