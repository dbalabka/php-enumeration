<?php
declare(strict_types=1);

namespace Dbalabka\Examples\Enum;

use Dbalabka\Enumeration;

final class Flag extends Enumeration
{
    public static $noState;
    public static $ok;
    public static $notOk;
    public static $unavailable;

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
Flag::initialize();
