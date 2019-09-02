<?php
declare(strict_types=1);

use Dbalabka\Enumeration\Examples\Enum\Color;
use Dbalabka\StaticConstructorLoader\StaticConstructorLoader;

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new StaticConstructorLoader($composer);

assert(Color::$red instanceof Color && Color::$red === Color::$red);
