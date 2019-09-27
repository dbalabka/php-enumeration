<?php
declare(strict_types=1);

use Dbalabka\Enumeration\Examples\Enum\Option;
use Dbalabka\StaticConstructorLoader\StaticConstructorLoader;

if (version_compare(PHP_VERSION, '7.4.0beta', '<')) {
    trigger_error('This code requires PHP >= 7.4', E_USER_NOTICE);
    return;
}

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new StaticConstructorLoader($composer);

function getResult(bool $returnResult): Option
{
    if ($returnResult) {
        return (Option::$some)(1);
    }
    return Option::$none;
}

function printResult(Option $option) : void
{
    if ($option instanceof Option::$some) {
        echo 'Return some value = ' . ($option->unwrap() + 1) . PHP_EOL;
    } elseif ($option instanceof Option::$none) {
        echo 'Return none' . PHP_EOL;
    } else {
        throw new Exception('Can not determine the result type');
    }
}

$option1 = getResult(true);
printResult($option1);
$option2 = getResult(false);
printResult($option2);


