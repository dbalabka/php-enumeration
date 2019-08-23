<?php
declare(strict_types=1);

use Dbalabka\Enumeration;

require_once(__DIR__ . '/../vendor/autoload.php');

final class CartType extends Enumeration {
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
CartType::initialize();

var_dump(CartType::values());


class Cart
{
    private $type;

    public function __construct(CartType $type)
    {
        $this->type = $type;
    }
}

$cart = new Cart(CartType::$amex);
var_dump(CartType::$amex === CartType::$visa);
var_dump(CartType::$amex === CartType::$amex);

foreach (CartType::values() as $type) {
    echo $type . "\n";
}

$a = CartType::$visa;
switch ($a) {
    case CartType::$amex:
        echo 'It is ' . CartType::$amex . PHP_EOL;
        break;
    case CartType::$visa:
        echo 'It is ' . CartType::$visa . PHP_EOL;
        break;
}


