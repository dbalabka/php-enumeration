<?php
declare(strict_types=1);

use Dbalabka\Examples\Enum\CardType;

require_once(__DIR__ . '/../vendor/autoload.php');

$values = CardType::values();
assert(is_array($values));
assert($values['amex'] === CardType::$amex);
assert($values['visa'] === CardType::$visa);
assert($values['masterCard'] === CardType::$masterCard);


class Card
{
    public $type;

    public function __construct(CardType $type)
    {
        $this->type = $type;
    }
}

$card = new Card(CardType::$amex);
assert($card->type !== CardType::$visa, 'We can use strict comparision, because each Enum\'s element is singleton');
assert($card->type === CardType::$amex);

foreach (CardType::values() as $name => $type) {
    assert($type instanceof CardType, 'Array values are Enums instances');
    assert($name === (string) $type, 'Array keys are Enums elements names');
}

$a = CardType::$visa;
switch ($a) {
    case CardType::$amex:
        assert(false, 'Incorrect, it is not amex');
        break;
    case CardType::$visa:
        assert(true, 'Correct, it is visa!');
        break;
}


