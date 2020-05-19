<?php declare(strict_types=1);

namespace Dbalabka\StaticConstructorLoader;

use Composer\Autoload\ClassLoader;
use Dbalabka\StaticConstructorLoader\Exception\StaticConstructorLoaderException;

class DebugStaticConstructorLoader extends ClassLoader /* extending for an contract */
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
        return $this->classLoader->loadClass($className);
    }

    public function getPrefixes()
    {
        return $this->classLoader->getPrefixes();
    }

    public function getPrefixesPsr4()
    {
        return $this->classLoader->getPrefixesPsr4();
    }

    public function getFallbackDirs()
    {
        return $this->classLoader->getFallbackDirs();
    }

    public function getFallbackDirsPsr4()
    {
        return $this->classLoader->getFallbackDirsPsr4();
    }

    public function getClassMap()
    {
        return $this->classLoader->getClassMap();
    }

    public function addClassMap(array $classMap)
    {
        $this->classLoader->addClassMap($classMap);
    }

    public function add($prefix, $paths, $prepend = false)
    {
        $this->classLoader->add($prefix, $paths, $prepend);
    }

    public function addPsr4($prefix, $paths, $prepend = false)
    {
        $this->classLoader->addPsr4($prefix, $paths, $prepend);
    }

    public function set($prefix, $paths)
    {
        $this->classLoader->set($prefix, $paths);
    }

    public function setPsr4($prefix, $paths)
    {
        $this->classLoader->setPsr4($prefix, $paths);
    }

    public function setUseIncludePath($useIncludePath)
    {
        $this->classLoader->setUseIncludePath($useIncludePath);
    }

    public function getUseIncludePath()
    {
        return $this->classLoader->getUseIncludePath();
    }

    public function setClassMapAuthoritative($classMapAuthoritative)
    {
        $this->classLoader->setClassMapAuthoritative($classMapAuthoritative);
    }

    public function isClassMapAuthoritative()
    {
        return $this->classLoader->isClassMapAuthoritative();
    }

    public function setApcuPrefix($apcuPrefix)
    {
        $this->classLoader->setApcuPrefix($apcuPrefix);
    }

    public function getApcuPrefix()
    {
        return $this->classLoader->getApcuPrefix();
    }

    public function register($prepend = false)
    {
        $this->classLoader->register($prepend);
    }

    public function unregister()
    {
        $this->classLoader->unregister();
    }

    public function findFile($class)
    {
        $path = $this->classLoader->findFile($class);

        if (
            class_exists(\Symfony\Component\ErrorHandler\DebugClassLoader::class, false)
            || class_exists(\Symfony\Component\Debug\DebugClassLoader::class, false)
        ) {
            return $this->handleDebugClassLoader($class, $path);
        }

        return $path;
    }

    private function handleDebugClassLoader($class, $path)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $debugClassLoader = ($backtrace[2] ?? null);

        if ($path
            && is_file($path)
            && \in_array(
                $debugClassLoader['class'] ?? null,
                [
                    \Symfony\Component\Debug\DebugClassLoader::class,
                    \Symfony\Component\ErrorHandler\DebugClassLoader::class
                ],
                true
            )
        ) {
            include $path;

            if (
                $class !== StaticConstructorInterface::class
                && is_a($class, StaticConstructorInterface::class, true)
            ) {
                $class::__constructStatic();
            }

            return false;
        }

        return $path;
    }
}