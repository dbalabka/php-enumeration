<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Tests\Fixtures;

use Dbalabka\Enumeration\Enumeration;

final class PublicConstructorEnum extends Enumeration
{
    public static $testValue;

    public function __construct()
    {
    }
}
