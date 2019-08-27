<?php
declare(strict_types=1);

use Dbalabka\Examples\Enum\Color;

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new ConstructStatic\Loader($composer);

assert(Color::$red instanceof Color && Color::$red === Color::$red);
