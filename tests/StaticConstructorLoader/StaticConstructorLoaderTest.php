<?php

namespace Dbalabka\StaticConstructorLoader\Tests;

use Composer\Autoload\ClassLoader;
use Dbalabka\StaticConstructorLoader\Exception\StaticConstructorLoaderException;
use Dbalabka\StaticConstructorLoader\StaticConstructorLoader;
use Dbalabka\StaticConstructorLoader\Tests\Fixtures\Action;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\Constraint\Exception as ConstraintException;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Prediction\AggregateException;

/**
 * @author Dmitrijs Balabka <dmitry.balabka@gmail.com>
 */
class StaticConstructorLoaderTest extends TestCase
{
    /** @var callable */
    public static $splAutoloadFunctionsCallback;

    /** @var callable */
    public static $splAutoloadUnregisterCallback;

    /** @var callable */
    public static $splAutoloadRegisterCallback;

    /**
     * @var ClassLoader|\Prophecy\Prophecy\ObjectProphecy
     */
    private $classLoader;

    /**
     * @var array
     */
    private $unregisteredAutoloaders = [];

    /**
     * @var array
     */
    private $registeredAutoloaders = [];

    private $oldAutoloadFunctions;

    protected function setUp(): void
    {
        $this->classLoader = $this->prophesize(ClassLoader::class);
        $this->saveAutoloaders();
        // preload classes
        class_exists(StaticConstructorLoader::class);
        class_exists(StaticConstructorLoaderException::class);
        class_exists(Exception::class);
        class_exists(Exception::class);
        class_exists(ConstraintException::class);
        class_exists(AggregateException::class);
    }

    protected function tearDown(): void
    {
        $this->restoreAutoloaders();
    }

    private function saveAutoloaders()
    {
        $this->oldAutoloadFunctions = \spl_autoload_functions();
    }

    private function restoreAutoloaders()
    {
        $this->clearAutoloaders();
        array_map('spl_autoload_register', $this->oldAutoloadFunctions);
    }

    private function clearAutoloaders()
    {
        array_map('spl_autoload_unregister', \spl_autoload_functions());
    }

    public function testConstructWithoutRegisteredAutoloaders()
    {
        $this->expectException(StaticConstructorLoaderException::class);
        $classLoader = $this->classLoader->reveal();

        $this->clearAutoloaders();
        new StaticConstructorLoader($classLoader);
    }

    public function testConstructWithRegisteredAutoloadersButWithoutComposerAutoloader()
    {
        $this->expectException(StaticConstructorLoaderException::class);
        $classLoader = $this->classLoader->reveal();

        array_map('spl_autoload_register', [
            function () {},
            [$this, 'testConstructWithRegisteredAutoloadersButWithoutComposerAutoloader']
        ]);
        new StaticConstructorLoader($classLoader);
    }

    public function testConstructWithAlreadyRegisteredStaticConstructorLoader()
    {
        $this->expectException(StaticConstructorLoaderException::class);
        $classLoader = $this->classLoader->reveal();
        $staticConstructorLoader = unserialize('O:56:"Dbalabka\StaticConstructorLoader\StaticConstructorLoader":1:{s:67:"Dbalabka\StaticConstructorLoader\StaticConstructorLoaderclassLoader";N;}');
        array_map('spl_autoload_register', [[$staticConstructorLoader, 'loadClass']]);
        new StaticConstructorLoader($classLoader);
    }

    public function testConstructSuccess()
    {
        $classLoader = $this->classLoader->reveal();
        array_map('spl_autoload_unregister', \spl_autoload_functions());
        array_map('spl_autoload_register', [
            $firstCallback = function () {},
            [$classLoader, 'loadClass'],
            $lastCallback = function () {},
        ]);
        self::$splAutoloadFunctionsCallback = 'spl_autoload_functions';

        $staticConstructorLoader = new StaticConstructorLoader($classLoader);
        $autoloaders = \spl_autoload_functions();

        $this->restoreAutoloaders();

        $this->assertSame(
            [
                $firstCallback,
                [$staticConstructorLoader, 'loadClass'],
                $lastCallback,
            ],
            $autoloaders
        );
    }

    public function testClassLoad()
    {
        $composerClassLoader = array_filter(spl_autoload_functions(), function ($v) {
            return is_array($v) && $v[0] instanceof ClassLoader;
        })[0][0];
        new StaticConstructorLoader($composerClassLoader);
        class_exists(Action::class);
        $this->assertInstanceOf(Action::class, Action::$instance);
    }

    public function testNotExistingClassLoad()
    {
        $composerClassLoader = array_filter(spl_autoload_functions(), function ($v) {
            return is_array($v) && $v[0] instanceof ClassLoader;
        })[0][0];
        new StaticConstructorLoader($composerClassLoader);
        $this->assertFalse(class_exists('NotExistingClass'));

    }
}
