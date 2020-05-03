<?php

namespace Dbalabka\Enumeration\Tests\Fixtures;

use Dbalabka\Enumeration\Enumeration;
use function version_compare;
use const PHP_VERSION;

if (version_compare(PHP_VERSION, '7.4.0beta', '<')) {
    require_once __DIR__ . '/ActionProperties.php';
} else {
    require_once __DIR__ . '/ActionTypedProperties.php';
}
final class ActionWithCustomStaticProperty extends AbstractAction
{
    use ActionProperties;

    public static $customProperty;
}
