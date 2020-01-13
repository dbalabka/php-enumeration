<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Enum;

use Dbalabka\Enumeration\Enumeration;

final class Flag extends Enumeration
{
    /** @var $this */
    public static $noState;
    /** @var $this */
    public static $ok;
    /** @var $this */
    public static $notOk;
    /** @var $this */
    public static $unavailable;

    /** @var int */
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
