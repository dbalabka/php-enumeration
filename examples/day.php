<?php
declare(strict_types=1);

use Dbalabka\Examples\Enum\Day;

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    throw new \Exception('This code requires PHP >= 7.4');
}

$composer = require_once(__DIR__ . '/../vendor/autoload.php');
$loader = new ConstructStatic\Loader($composer);

class EnumTest
{
    private $day;

    public function __construct(Day $day) {
        $this->day = $day;
    }

    public function tellItLikeItIs() {
        switch ($this->day) {
            case Day::$monday:
                echo "Mondays are bad.\n";
                break;

            case Day::$friday:
                echo "Fridays are better.\n";
                break;

            case Day::$saturday:
            case Day::$sunday:
                echo "Weekends are best.\n";
                break;

            default:
                echo "Midweek days are so-so.\n";
                break;
        }
    }
}

$firstDay = new EnumTest(Day::$monday);
$firstDay->tellItLikeItIs();
$thirdDay = new EnumTest(Day::$wednesday);
$thirdDay->tellItLikeItIs();
$fifthDay = new EnumTest(Day::$friday);
$fifthDay->tellItLikeItIs();
$sixthDay = new EnumTest(Day::$saturday);
$sixthDay->tellItLikeItIs();
$seventhDay = new EnumTest(Day::$sunday);
$seventhDay->tellItLikeItIs();
