<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Examples\Enum;

use Dbalabka\Enumeration\Enumeration;

final class Day extends Enumeration
{
    public static Day $sunday;
    public static Day $monday;
    public static Day $tuesday;
    public static Day $wednesday;
    public static Day $thursday;
    public static Day $friday;
    public static Day $saturday;
}
