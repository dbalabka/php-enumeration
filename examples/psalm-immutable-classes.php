<?php
declare(strict_types=1);

use Dbalabka\Enumeration\Examples\Enum\CardType;
use Dbalabka\StaticConstructorLoader\StaticConstructorLoader;

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new StaticConstructorLoader($composer);

CardType::$amex = CardType::$masterCard;
