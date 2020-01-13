<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Enum;

use Dbalabka\Enumeration\Enumeration;

final class CardType extends Enumeration
{
    /** @var $this */
    public static $amex;
    /** @var $this */
    public static $visa;
    /** @var $this */
    public static $masterCard;

    protected static function initializeValues() : void
    {
        self::$amex = new self();
        self::$visa = new self();
        self::$masterCard  = new self();
    }
}
CardType::initialize();
