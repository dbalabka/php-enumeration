<?php
declare(strict_types=1);

use Dbalabka\Enumeration;

require_once(__DIR__ . '/../vendor/autoload.php');

final class CardType extends Enumeration {
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

var_dump(CardType::values());


class Card
{
    private $type;

    public function __construct(CardType $type)
    {
        $this->type = $type;
    }
}

$card = new Card(CardType::$amex);
var_dump(CardType::$amex === CardType::$visa);
var_dump(CardType::$amex === CardType::$amex);

foreach (CardType::values() as $type) {
    echo $type . "\n";
}

$a = CardType::$visa;
switch ($a) {
    case CardType::$amex:
        echo 'It is ' . CardType::$amex . PHP_EOL;
        break;
    case CardType::$visa:
        echo 'It is ' . CardType::$visa . PHP_EOL;
        break;
}


