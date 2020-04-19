<?php

declare(strict_types=1);

namespace Tests;

use \Exception;
//
use Litea\DataTransfer\DataObject;
use Litea\DataTransfer\Exceptions\MissingExpectedPropertyException;
//
use PHPUnit\Framework\TestCase;

class DemoClass extends DataObject {}

class DataObjectTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_the_instance_of_dto_class()
    {
        $data = null;

        try {
            $data = DemoClass::create();
        } catch (Exception $exception) {}

        $this->assertInstanceOf(DemoClass::class, $data);
    }

    /**
     * @test
     */
    public function it_does_the_basic_data_transfer()
    {
        $data = [
            'myString' => 'Foo',
            'myInt' => 42,
            'myBool' => true
        ];

        $dto = (new class extends DataObject {
            public $myString;
            public $myInt;
            public $myBool;
        })::create($data);

        $this->assertEquals($data['myString'], $dto->myString);
        $this->assertEquals($data['myInt'], $dto->myInt);
        $this->assertEquals($data['myBool'], $dto->myBool);
    }

    /**
     * @test
     */
    public function it_can_handle_shallow_primitive_types()
    {
        $data = [
            'remoteString' => 'Foo',
            'remoteInt' => 42,
            'remoteBool' => true
        ];

        $dto = (new class extends DataObject {
            /**
             * @var string
             * @dto-property remoteString
             */
            public $myString;

            /**
             * @var int
             * @dto-property remoteInt
             */
            public $myInt;

            /**
             * @var bool
             * @dto-property remoteBool
             */
            public $myBool;
        })::create($data);

        $this->assertEquals($data['remoteString'], $dto->myString);
        $this->assertEquals($data['remoteInt'], $dto->myInt);
        $this->assertEquals($data['remoteBool'], $dto->myBool);
    }

    /**
     * @test
     */
    public function it_preserves_default_values()
    {
        $dto = (new class extends DataObject {
            /**
             * @var string
             * @dto-property remoteString
             */
            public $myString = 'Lorem ipsum';
        })::create([]);

        $this->assertEquals('Lorem ipsum', $dto->myString);
    }

    /**
     * @test
     */
    public function it_uses_setter_when_present()
    {
        $data = ['myString' => 'Hello World!'];

        $dto = (new class extends DataObject {
            public $myString;

            public function setMyString($value)
            {
                $this->myString = str_replace('World', 'Universe', $value);
            }
        })::create($data);

        $this->assertEquals('Hello Universe!', $dto->myString);
    }

    /**
     * @test
     */
    public function it_tries_to_cast_the_value()
    {
        $data = [
            'myInt' => '123Foo',
            'myBool' => '0'
        ];

        $dto = (new class extends DataObject {
            /**
             * @var int
             */
            public $myInt;

            /**
             * @var bool
             */
            public $myBool;
        })::create($data);

        $this->assertEquals(123, $dto->myInt);
        $this->assertFalse($dto->myBool);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_mandatory_property_is_missing()
    {
        $this->expectException(MissingExpectedPropertyException::class);

        (new class extends DataObject {
            public $myInt;
            public $myBool;
        })::create([]);
    }

    /**
     * @test
     */
    public function it_uses_only_selected_properties_when_instructed()
    {
        $data = [
            'myString' => 'Foo',
            'ignoredString' => 'Bar'
        ];

        $dto = (new class extends DataObject {
            protected static $allowImplicit = false;

            /** @dto-property myString */
            public $myString;

            public $ignoredString = 'Lorem ipsum';
        })::create($data);

        $this->assertEquals($data['myString'], $dto->myString);
        $this->assertEquals('Lorem ipsum', $dto->ignoredString);
    }
}