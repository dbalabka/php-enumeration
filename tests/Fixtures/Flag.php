<?php
declare(strict_types=1);

namespace Dbalabka\Tests\Fixtures;

use Dbalabka\Enumeration;
use const PHP_VERSION_ID;

if (PHP_VERSION_ID >= 70400) {
    require_once __DIR__ . '/FlagTypedProperties.php';
} else {
    require_once __DIR__ . '/FlagProperties.php';
}
final class Flag extends Enumeration
{
    use FlagProperties;

    private $flagValue;

    protected function __construct()
    {
        $this->flagValue = 1 << $this->ordinal();
    }

    public function getFlagValue() : int
    {
        return $this->flagValue;
    }
}
