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

/**
 * @template T
 * @psalm-param bool $returnResult
 * @psalm-param T $value
 * @psalm-return Option<T>|Option<null>
 */
function getResult(bool $returnResult, $value)
{
    if ($returnResult) {
        return (Option::$some)($value);
    }
    return Option::$none;
}

/**
 * @psalm-param Option<int>|Option<null> $option
 */
function printResult(Option $option) : void
{
    if ($option instanceof Option::$some) {
        /**
         * @psalm-suppress PossiblyNullOperand psalm can not properly determine that it is a Some and use int|null type.
         *                 We need to write custom type inference from $option if it is instance of Option::$some and same for Option::$none
         */
        echo 'Return some value = ' . ($option->unwrap() + 1) . PHP_EOL;
    } elseif ($option instanceof Option::$none) {
        echo 'Return none' . PHP_EOL;
    } else {
        throw new Exception('Can not determine the result type');
    }
}

$option1 = getResult(true, '1');
/** @psalm-suppress PossiblyInvalidArgument Psalm correctly catches incorrect passed type */
printResult($option1);
$option1 = getResult(true, 1);
printResult($option1);
$option2 = getResult(false, 1);
printResult($option2);


