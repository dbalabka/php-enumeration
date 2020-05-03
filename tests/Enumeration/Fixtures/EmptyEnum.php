<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Tests\Fixtures;

use Dbalabka\Enumeration\Enumeration;

final class EmptyEnum extends Enumeration
{
    protected function __construct()
    {
        $this->ordinal();
    }
}
