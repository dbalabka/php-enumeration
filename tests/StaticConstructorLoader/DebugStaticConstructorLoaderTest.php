<?php declare(strict_types=1);

namespace Dbalabka\Enumeration\Tests\StaticConstructorLoader;

use Dbalabka\StaticConstructorLoader\DebugStaticConstructorLoader;
use Dbalabka\StaticConstructorLoader\StaticConstructorLoader;
use Dbalabka\StaticConstructorLoader\Tests\Fixtures\ChildOfAbstractEnumeration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ErrorHandler\DebugClassLoader;

class DebugStaticConstructorLoaderTest extends TestCase
{
    /** @var callable */
    private $defaultLoader;

    protected function setUp()
    {
        $this->defaultLoader = \spl_autoload_functions()[0];
    }

    protected function tearDown()
    {
        DebugClassLoader::disable();
    }

    /**
     * @runInSeparateProcess
     */
    public function testClassLoadWithDefaultStaticConstrcutorLoader()
    {
        new StaticConstructorLoader($this->defaultLoader[0]);
        $x = ChildOfAbstractEnumeration::$instance;
        $this->assertInstanceOf(ChildOfAbstractEnumeration::class, $x);
    }

    /**
     * @runInSeparateProcess
     */
    public function testClassLoadWithDefaultStaticConstrcutorLoaderAndSymfonyDebugLoader()
    {
        new StaticConstructorLoader($this->defaultLoader[0]);
        (new DebugClassLoader($this->defaultLoader))::enable();
        $x = ChildOfAbstractEnumeration::$instance;
        $this->assertNull($x);
    }

    /**
     * @runInSeparateProcess
     */
    public function testClassLoadWithDebugStaticConstrcutorLoaderAndSymfonyDebugLoader()
    {
        new DebugStaticConstructorLoader($this->defaultLoader[0]);
        (new DebugClassLoader($this->defaultLoader))::enable();
        $x = ChildOfAbstractEnumeration::$instance;
        $this->assertInstanceOf(ChildOfAbstractEnumeration::class, $x);
    }
}
