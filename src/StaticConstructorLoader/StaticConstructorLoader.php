<?php
declare(strict_types=1);

namespace Dbalabka\StaticConstructorLoader;

use Composer\Autoload\ClassLoader;
use Dbalabka\StaticConstructorLoader\Exception\StaticConstructorLoaderException;

/**
 * Decorates the Composer autoloader to statically initialize the class.
 * This is a very lightweight workaround which is described in https://wiki.php.net/rfc/static_class_constructor
 *
 * @author Dmitrijs Balabka <dmitry.balabka@gmail.com>
 */
final class StaticConstructorLoader
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;

        // find Composer autoloader
        $loaders = spl_autoload_functions();
        $otherLoaders = [];
        $composerLoader = null;
        foreach ($loaders as $loader) {
            if (is_array($loader)) {
                if ($loader[0] === $classLoader) {
                    $composerLoader = $loader;
                    break;
                }
                if ($loader[0] instanceof self) {
                    throw new StaticConstructorLoaderException(sprintf('%s already registered', self::class));
                }
            }
            $otherLoaders[] = $loader;
        }

        if (!$composerLoader) {
            throw new StaticConstructorLoaderException(sprintf('%s was not found in registered autoloaders', ClassLoader::class));
        }

        // unregister Composer autoloader and all preceding autoloaders
        array_map('spl_autoload_unregister', array_merge($otherLoaders, [$composerLoader]));

        // restore the original queue order
        $loadersToRestore = array_merge([[$this, 'loadClass']], array_reverse($otherLoaders));
        $flagTrue = array_fill(0, count($loadersToRestore), true);
        array_map('spl_autoload_register', $loadersToRestore, $flagTrue, $flagTrue);
    }

    public function loadClass(string $className): ?bool
    {
        $result = $this->classLoader->loadClass($className);
        if ($result === true && $className !== StaticConstructorInterface::class && is_a($className, StaticConstructorInterface::class, true)) {
            $className::__constructStatic();
        }
        return $result;
    }
}
