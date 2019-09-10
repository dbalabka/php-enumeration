<?php
declare(strict_types=1);

use Dbalabka\Enumeration\Exception\EnumerationException;
use Dbalabka\Enumeration\Examples\Enum\Color;
use Dbalabka\StaticConstructorLoader\StaticConstructorLoader;

if (version_compare(PHP_VERSION, '7.4.0beta', '<')) {
    trigger_error('This code requires PHP >= 7.4', E_USER_NOTICE);
    return;
}

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new StaticConstructorLoader($composer);

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
