<?php
declare(strict_types=1);

namespace Dbalabka\StaticConstructorLoader;

/**
 * @author Dmitrijs Balabka <dmitry.balabka@gmail.com>
 */
interface StaticConstructorInterface
{
    /**
     * This method should be called right after enumerate class declaration.
     * Unfortunately, PHP does not support static initialization.
     * See static init RFC: https://wiki.php.net/rfc/static_class_constructor
     */
    public static function __constructStatic();
}
