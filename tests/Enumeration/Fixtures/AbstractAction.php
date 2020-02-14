<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Tests\Fixtures;

use Dbalabka\Enumeration\Enumeration;
use function version_compare;
use const PHP_VERSION;

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    require_once __DIR__ . '/ActionProperties.php';
} else {
    require_once __DIR__ . '/ActionTypedProperties.php';
}

/**
 * Final is omitted for testing purposes
 */
abstract class AbstractAction extends Enumeration
{
    use ActionProperties;
}
