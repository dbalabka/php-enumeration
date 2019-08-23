<?php
declare(strict_types=1);

namespace Dbalabka\Tests\Fixtures;

use Dbalabka\Enumeration;

final class ActionWithPublicConstructor extends Enumeration
{
    public static Action $view;
    public static Action $edit;

    public function __construct()
    {
        parent::__construct();
    }
}

