<?php
declare(strict_types=1);

use Dbalabka\Examples\Fixtures\Planet;

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new ConstructStatic\Loader($composer);

$earthWeight = 175;
$mass = $earthWeight / Planet::$earth->surfaceGravity();
foreach (Planet::values() as $p) {
    printf("Your weight on %s is %s\n", $p, $p->surfaceWeight($mass));
}
