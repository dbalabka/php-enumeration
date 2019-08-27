<?php
declare(strict_types=1);

namespace Dbalabka\Tests\Fixtures;

use Dbalabka\Enumeration;
use const PHP_VERSION_ID;

if (PHP_VERSION_ID >= 70400) {
    require_once __DIR__ . '/ActionTypedProperties.php';
} else {
    require_once __DIR__ . '/ActionProperties.php';
}
final class ActionWithPublicConstructor extends Enumeration
{
    use ActionProperties;

    public function __construct()
    {
        parent::__construct();
    }
}

