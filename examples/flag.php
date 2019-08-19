<?php
declare(strict_types=1);

use Dbalabka\Enumeration;

require_once(__DIR__ . '/../vendor/autoload.php');

final class Flag extends Enumeration
{
    public static $red;
    public static $blue;
    public static $green;

    protected static function initializeValues(): void
    {
        self::$red = new self(1);
        self::$blue = new self(2);
        self::$green = new self(4);
    }
}
Flag::initialize();

var_dump(Flag::values());

$red = Flag::$red;
$green = Flag::$green;
var_dump('a', Flag::$red < Flag::$green);

// Operators overloading is not supported by PHP (see https://wiki.php.net/rfc/operator-overloading)
var_dump('b', Flag::$red & Flag::$green);

try {
    var_dump('c', $red() & $green());
} catch (\Throwable $e) {
    echo 'Error: ' . $e->getMessage();
}
var_dump('d', 1 & 2);

try {
    var_dump('e', (Flag::$red)() & (Flag::$green)());
} catch (\Throwable $e) {
    echo 'Error: ' . $e->getMessage();
}
