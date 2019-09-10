<?php
declare(strict_types=1);

use Dbalabka\Enumeration\Examples\Enum\Planet;
use Dbalabka\StaticConstructorLoader\StaticConstructorLoader;

if (version_compare(PHP_VERSION, '7.4.0beta', '<')) {
    trigger_error('This code requires PHP >= 7.4', E_USER_NOTICE);
    return;
}

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new StaticConstructorLoader($composer);

$earthWeight = 175;
$mass = $earthWeight / Planet::$earth->surfaceGravity();
foreach (Planet::values() as $p) {
    printf("Your weight on %s is %s\n", $p, $p->surfaceWeight($mass));
}
