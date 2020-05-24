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
final class StaticConstructorLoader extends ClassLoader /* extending for an contract */
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

    public function loadClass($className): ?bool
    {
        $result = $this->classLoader->loadClass($className);
        if ($result === true && $className !== StaticConstructorInterface::class && is_a($className, StaticConstructorInterface::class, true)) {
            $className::__constructStatic();
        }
        return $result;
    }

    /** @codeCoverageIgnore */
    public function getPrefixes()
    {
        return $this->classLoader->getPrefixes();
    }

    /** @codeCoverageIgnore */
    public function getPrefixesPsr4()
    {
        return $this->classLoader->getPrefixesPsr4();
    }

    /** @codeCoverageIgnore */
    public function getFallbackDirs()
    {
        return $this->classLoader->getFallbackDirs();
    }

    /** @codeCoverageIgnore */
    public function getFallbackDirsPsr4()
    {
        return $this->classLoader->getFallbackDirsPsr4();
    }

    /** @codeCoverageIgnore */
    public function getClassMap()
    {
        return $this->classLoader->getClassMap();
    }

    /** @codeCoverageIgnore */
    public function addClassMap(array $classMap)
    {
        $this->classLoader->addClassMap($classMap);
    }

    /** @codeCoverageIgnore */
    public function add($prefix, $paths, $prepend = false)
    {
        $this->classLoader->add($prefix, $paths, $prepend);
    }

    /** @codeCoverageIgnore */
    public function addPsr4($prefix, $paths, $prepend = false)
    {
        $this->classLoader->addPsr4($prefix, $paths, $prepend);
    }

    /** @codeCoverageIgnore */
    public function set($prefix, $paths)
    {
        $this->classLoader->set($prefix, $paths);
    }

    /** @codeCoverageIgnore */
    public function setPsr4($prefix, $paths)
    {
        $this->classLoader->setPsr4($prefix, $paths);
    }

    /** @codeCoverageIgnore */
    public function setUseIncludePath($useIncludePath)
    {
        $this->classLoader->setUseIncludePath($useIncludePath);
    }

    /** @codeCoverageIgnore */
    public function getUseIncludePath()
    {
        return $this->classLoader->getUseIncludePath();
    }

    /** @codeCoverageIgnore */
    public function setClassMapAuthoritative($classMapAuthoritative)
    {
        $this->classLoader->setClassMapAuthoritative($classMapAuthoritative);
    }

    /** @codeCoverageIgnore */
    public function isClassMapAuthoritative()
    {
        return $this->classLoader->isClassMapAuthoritative();
    }

    /** @codeCoverageIgnore */
    public function setApcuPrefix($apcuPrefix)
    {
        $this->classLoader->setApcuPrefix($apcuPrefix);
    }

    /** @codeCoverageIgnore */
    public function getApcuPrefix()
    {
        return $this->classLoader->getApcuPrefix();
    }

    /** @codeCoverageIgnore */
    public function register($prepend = false)
    {
        $this->classLoader->register($prepend);
    }

    /** @codeCoverageIgnore */
    public function unregister()
    {
        $this->classLoader->unregister();
    }

    /** @codeCoverageIgnore */
    public function findFile($class)
    {
        return $this->classLoader->findFile($class);
    }
}
