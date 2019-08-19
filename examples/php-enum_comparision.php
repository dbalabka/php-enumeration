<?php
declare(strict_types=1);

use Dbalabka\Enumeration;
use Dbalabka\Examples\Fixtures\Color;
use MyCLabs\Enum\Enum;
use Dbalabka\Examples\Fixtures\Action;

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new ConstructStatic\Loader($composer);

$viewAction = Action::$view;

