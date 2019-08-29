<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Tests\Fixtures;

use Dbalabka\Enumeration\Enumeration;
use function version_compare;
use const PHP_VERSION;

if (version_compare(PHP_VERSION, '7.4.0beta', '<')) {
    require_once __DIR__ . '/FlagProperties.php';
} else {
    require_once __DIR__ . '/FlagTypedProperties.php';
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
