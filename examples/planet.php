<?php
declare(strict_types=1);

use Dbalabka\Examples\Enum\Planet;

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    throw new \Exception('This code requires PHP >= 7.4');
}

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new ConstructStatic\Loader($composer);

$earthWeight = 175;
$mass = $earthWeight / Planet::$earth->surfaceGravity();
foreach (Planet::values() as $p) {
    printf("Your weight on %s is %s\n", $p, $p->surfaceWeight($mass));
}
