<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Enum;

use Dbalabka\Enumeration\Enumeration;

final class CardType extends Enumeration
{
    /** @var self */
    public static $amex;
    /** @var self */
    public static $visa;
    /** @var self */
    public static $masterCard;

    protected static function initializeValues() : void
    {
        self::$amex = new self();
        self::$visa = new self();
        self::$masterCard  = new self();
    }
}
CardType::initialize();
