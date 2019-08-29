<?php
declare(strict_types=1);

use Dbalabka\Enumeration\Examples\Enum\Flag;

require_once(__DIR__ . '/../vendor/autoload.php');

assert(Flag::$noState < Flag::$ok);
assert(Flag::$noState < Flag::$notOk);
assert(Flag::$noState < Flag::$unavailable);
assert(Flag::$ok < Flag::$notOk);
assert(Flag::$ok < Flag::$unavailable);
assert(Flag::$notOk < Flag::$unavailable);

set_error_handler(function ($errno, $errstr) {
    assert($errstr === sprintf('Object of class %s could not be converted to int', Flag::class));
});
// Operators overloading is not supported by PHP (see https://wiki.php.net/rfc/operator-overloading)
assert(1 === (Flag::$notOk & Flag::$unavailable));
restore_error_handler();
