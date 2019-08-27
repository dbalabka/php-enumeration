<?php
declare(strict_types=1);

use Dbalabka\Examples\Enum\Action;

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    trigger_error('This code requires PHP >= 7.4', E_USER_NOTICE);
    return;
}

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new ConstructStatic\Loader($composer);

$viewAction = Action::$view;

// TODO: implement
