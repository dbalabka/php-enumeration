<?php
declare(strict_types=1);

use Dbalabka\Enumeration;
use Dbalabka\Examples\Fixtures\Color;

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new ConstructStatic\Loader($composer);

var_dump(Color::$red instanceof Color && Color::$red === Color::$red);
