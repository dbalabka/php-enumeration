<?php
declare(strict_types=1);

namespace Dbalabka\Enumeration\Tests;

use Dbalabka\Enumeration\Enumeration;
use Dbalabka\Enumeration\Exception\EnumerationException;
use Dbalabka\Enumeration\Exception\InvalidArgumentException;
use Dbalabka\Enumeration\Tests\Fixtures\Action;
use Dbalabka\Enumeration\Tests\Fixtures\ActionWithCustomStaticProperty;
use Dbalabka\Enumeration\Tests\Fixtures\ActionWithPublicConstructor;
use Dbalabka\Enumeration\Tests\Fixtures\EmptyEnum;
use Dbalabka\Enumeration\Tests\Fixtures\Flag;
use Dbalabka\Enumeration\Tests\Fixtures\NotFinalEnum;
use Dbalabka\Enumeration\Tests\Fixtures\PublicConstructorEnum;
use Error;
use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;
use function serialize;
use function version_compare;
use const PHP_VERSION;

class EnumerationTest extends TestCase
{
    public function testInstantiate()
    {
        $this->expectException(Error::class);
        new Action();
    }

    public function testAnonEnum()
    {
        $this->expectException(Error::class);
        new class extends Enumeration {};
    }

    public function testInstantiateWithPublicConstructor()
    {
        $this->assertInstanceOf(Enumeration::class, new ActionWithPublicConstructor());
    }

    public function testAccessNotInitilizedEnumItemWithTypedProperties()
    {
        if (version_compare(PHP_VERSION, '7.4.0beta', '<')) {
            $this->markTestSkipped('This test requires typed properties support');
        }
        $this->expectException(Error::class);
        Action::$view;
    }

    public function testOrdinals()
    {
        Action::initialize();
        $this->assertSame(0, Action::$view->ordinal());
        $this->assertSame(1, Action::$edit->ordinal());
    }

    public function testAccessOrdinalsInConstructor()
    {
        Flag::initialize();
        $this->assertSame(1, Flag::$noState->getFlagValue());
        $this->assertSame(2, Flag::$ok->getFlagValue());
        $this->assertSame(4, Flag::$notOk->getFlagValue());
        $this->assertSame(8, Flag::$unavailable->getFlagValue());
    }

    public function testName()
    {
        Flag::initialize();

        $this->assertSame('ok', Flag::$ok->name());
        $this->assertSame('notOk', Flag::$notOk->name());
    }

    public function testToString()
    {
        Flag::initialize();

        $this->assertSame('ok', '' . Flag::$ok);
        $this->assertSame('notOk', '' . Flag::$notOk);
    }

    public function testEquals()
    {
        Flag::initialize();

        $notOk = Flag::$notOk;
        $this->assertSame($notOk, Flag::$notOk);
        $this->assertTrue($notOk === Flag::$notOk);
    }

    public function testSerialization()
    {
        Flag::initialize();

        $this->expectException(EnumerationException::class);
        serialize(Flag::$notOk);
    }

    public function testUnserialization()
    {
        Flag::initialize();
        $this->expectException(EnumerationException::class);
        if (version_compare(PHP_VERSION, '7.4.0-dev', '<')) {
            unserialize('C:40:"Dbalabka\\Enumeration\\Tests\\Fixtures\\Flag":24:{a:1:{s:7:"ordinal";i:2;}}');
        } else {
            unserialize('O:40:"Dbalabka\\Enumeration\\Tests\\Fixtures\\Flag":1:{s:7:"ordinal";i:2;}');
        }
    }

    public function testClone()
    {
        Flag::initialize();

        $this->expectException(EnumerationException::class);
        $cloned = clone Flag::$notOk;
    }

    public function testValueOf()
    {
        Flag::initialize();

        $this->assertSame(Flag::$ok, Flag::valueOf('ok'));
        $this->assertSame(Flag::$notOk, Flag::valueOf('notOk'));
    }

    public function testValueOfNotExistingName()
    {
        Flag::initialize();

        $this->expectException(InvalidArgumentException::class);
        Flag::valueOf('does not exists');
    }

    public function testValues()
    {
        Flag::initialize();

        $values = Flag::values();

        $this->assertSame(
            $values,
            [
                'noState' => Flag::$noState,
                'ok' => Flag::$ok,
                'notOk' => Flag::$notOk,
                'unavailable' => Flag::$unavailable,
            ]
        );
    }

    public function testSwitchSupport()
    {
        Action::initialize();
        $someAction = Action::$view;
        switch ($someAction) {
            case Action::$edit:
                $this->fail('Edit is not equal to view');
                break;
            case Action::$view:
                $this->addToAssertionCount(1);
                break;
            default:
                $this->fail('Default should not be called');
        }
    }

    public function testCompareTo()
    {
        Flag::initialize();

        $this->assertSame(2, Flag::$unavailable->compareTo(Flag::$ok));
        $this->assertSame(1, Flag::$ok->compareTo(Flag::$noState));
        $this->assertSame(0, Flag::$ok->compareTo(Flag::$ok));
        $this->assertSame(-1, Flag::$ok->compareTo(Flag::$notOk));
        $this->assertSame(-2, Flag::$ok->compareTo(Flag::$unavailable));
    }

    public function testCustomStaticProperties()
    {
        $this->markTestSkipped('Custom static properties are not allowed');
        ActionWithCustomStaticProperty::initialize();

        ActionWithCustomStaticProperty::$customProperty = ActionWithCustomStaticProperty::$edit;

        $this->assertSame(
            [
                'view' => ActionWithCustomStaticProperty::$view,
                'edit' => ActionWithCustomStaticProperty::$edit,
            ],
            ActionWithCustomStaticProperty::values()
        );

        $this->expectException(InvalidArgumentException::class);
        ActionWithCustomStaticProperty::valueOf('customProperty');
    }

    public function testNameWhenIncorrectlyInitilizedProperies()
    {
        Flag::initialize();

        $notOk = Flag::$notOk;
        Flag::$notOk = Flag::$noState;

        $this->expectException(EnumerationException::class);
        $notOk->name();
    }

    public function testNotFinalEnumShouldThrowAnException()
    {
        $this->expectException(EnumerationException::class);
        $this->expectExceptionMessage('Enumeration class should be declared as final');
        NotFinalEnum::initialize();
        NotFinalEnum::$testValue;
    }

    public function testPublicConstructorShouldThrowAnException()
    {
        $this->expectException(EnumerationException::class);
        $this->expectExceptionMessage('Enumeration class constructor should not be public');
        PublicConstructorEnum::initialize();
        PublicConstructorEnum::$testValue;
    }

    public function testOrdinalInitilizationFailedShouldThrowException()
    {
        $this->expectException(EnumerationException::class);
        $this->expectExceptionMessage('Ordinal initialization failed. Enum does not contain any static variables');
        EmptyEnum::initialize();
    }
}
