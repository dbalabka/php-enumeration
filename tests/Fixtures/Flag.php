<?php
declare(strict_types=1);

namespace Dbalabka\Tests\Fixtures;

use Dbalabka\Enumeration;

final class Flag extends Enumeration
{
    public static Flag $noState;
    public static Flag $ok;
    public static Flag $notOk;
    public static Flag $unavailable;

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
