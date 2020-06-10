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

    /** @codeCoverageIgnore */
    public function loadClass($className): ?bool
    {
        return $this->classLoader->loadClass($className);
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

    public function findFile($class)
    {
        $path = $this->classLoader->findFile($class);

        if (
            class_exists('Symfony\Component\ErrorHandler\DebugClassLoader', false)
            || class_exists('Symfony\Component\Debug\DebugClassLoader', false)
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
                ['Symfony\Component\Debug\DebugClassLoader', 'Symfony\Component\ErrorHandler\DebugClassLoader'],
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