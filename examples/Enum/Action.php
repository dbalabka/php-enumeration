<?php
declare(strict_types=1);

namespace Dbalabka\Examples\Enum;

use Dbalabka\Enumeration;

final class Action extends Enumeration
{
    public static Action $view;
    public static Action $edit;
}
