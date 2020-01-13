<?php
declare(strict_types=1);

use Dbalabka\Enumeration\Examples\Enum\Shape;
use Dbalabka\Enumeration\Examples\Struct\Point;
use Dbalabka\StaticConstructorLoader\StaticConstructorLoader;

if (version_compare(PHP_VERSION, '7.4.0beta', '<')) {
    trigger_error('This code requires PHP >= 7.4', E_USER_NOTICE);
    return;
}

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new StaticConstructorLoader($composer);

try {
    // Unfortunately, this will not be possible because of https://www.php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.indirect
    /** @psalm-suppress UndefinedGlobalVariable */
    $circ1 = Shape::$circle(new Point(1.0, 1.0), 5.0);
    assert(false);
} catch (Error $e) {
    assert(true);
}
$circ1 = (Shape::$circle)(new Point(1.0, 1.0), 5.0);
$rect1 = (Shape::$rectangle)(new Point(1.0, 1.0), new Point(2.0, 2.0));
assert($circ1 instanceof Shape);
assert($rect1 instanceof Shape);
assert($circ1 !== $rect1);
assert($circ1 != $rect1);
assert($circ1 === $circ1);
assert($circ1 == $circ1);
assert($circ1 !== Shape::$circle);
assert($circ1 != Shape::$circle);
