<?php
declare(strict_types=1);

namespace Dbalabka\Examples\Enum;

use Dbalabka\Enumeration;

final class CardType extends Enumeration
{
    public static $amex;
    public static $visa;
    public static $masterCard;

    protected static function initializeValues() : void
    {
        self::$amex = new self();
        self::$visa = new self();
        self::$masterCard  = new self();
    }
}
CardType::initialize();
