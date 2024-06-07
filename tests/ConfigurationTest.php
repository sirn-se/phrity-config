<?php

declare(strict_types=1);

namespace Phrity\Config;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ConfigurationTest extends TestCase
{
    public function testConfiguration(): void
    {
        $config = new Configuration();
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
        $this->assertInstanceOf(ContainerInterface::class, $config);
        $this->assertInstanceOf(JsonSerializable::class, $config);
    }

    public function testHas(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/complex.json');

        $this->assertTrue($config->has('string'));
        $this->assertTrue($config->has('object'));
        $this->assertTrue($config->has('object/string'));
        $this->assertTrue($config->has('object/object'));
        $this->assertTrue($config->has('object/object/string'));
        $this->assertTrue($config->has('object/array/'));
        $this->assertTrue($config->has('object/array/0'));
        $this->assertTrue($config->has('array'));
        $this->assertTrue($config->has('array/0'));
        $this->assertTrue($config->has('array/1'));
        $this->assertTrue($config->has('array/1/string'));
        $this->assertTrue($config->has('array/2'));
        $this->assertTrue($config->has('array/2/0'));

        $this->assertFalse($config->has('no'));
        $this->assertFalse($config->has('object/false'));
        $this->assertFalse($config->has('array/4'));
    }

    public function testGet(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/complex.json');

        $this->assertEquals('str', $config->get('string'));
        $this->assertEquals((object)[
            'string' => 'obj-str',
            'object' => (object)['string' => 'obj-obj-str'],
            'array' => ['obj-arr-str'],
        ], $config->get('object'));
        $this->assertEquals('obj-str', $config->get('object/string'));
        $this->assertEquals((object)['string' => 'obj-obj-str'], $config->get('object/object'));
        $this->assertEquals('obj-obj-str', $config->get('object/object/string'));
        $this->assertEquals(['obj-arr-str'], $config->get('object/array/'));
        $this->assertEquals('obj-arr-str', $config->get('object/array/0'));
        $this->assertEquals([
            'arr-str',
            (object)['string' => 'arr-obj-str'],
            ['arr-arr-str'],
        ], $config->get('array'));
        $this->assertEquals('arr-str', $config->get('array/0'));
        $this->assertEquals((object)['string' => 'arr-obj-str'], $config->get('array/1'));
        $this->assertEquals('arr-obj-str', $config->get('array/1/string'));
        $this->assertEquals(['arr-arr-str'], $config->get('array/2'));
        $this->assertEquals('arr-arr-str', $config->get('array/2/0'));
    }

    public function testGetDefault(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/complex.json');

        $this->assertEquals('No', $config->get('no', default: 'No'));
        $this->assertEquals('False', $config->get('object/false', default: 'False'));
        $this->assertEquals('4', $config->get('array/4', default: '4'));
    }

    public function testNotFound(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/complex.json');

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("No configuration entry with id 'no'.");
        $config->get('no');
    }

    public function testJsonSerialize(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/valid.json');

        $this->assertEquals((object)[
            'a' => (object)['a' => 1, 'b' => 2],
            'b' => [23],
        ], $config->jsonSerialize());
    }

    public function testMerge(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/complex.json');
        $extend = $reader->createConfiguration(__DIR__ . '/fixtures/merge.json');
        $merged = $config->merge($extend);

        // Original must be intact
        $this->assertEquals((object)[
            'string' => 'str',
            'object' => (object)[
                'string' => 'obj-str',
                'object' => (object)[
                    'string' => 'obj-obj-str',
                ],
                'array' => [
                    'obj-arr-str',
                ],
            ],
            'array' => [
                'arr-str',
                (object)[
                    'string' => 'arr-obj-str',
                ],
                [
                    'arr-arr-str',
                ],
            ],
        ], $config->jsonSerialize());

        $this->assertEquals((object)[
            'string' => 'overwritten-str',
            'object' => (object)[
                'string' => 'overwritten-obj-str',
                'object' => (object)[
                    'string' => 'obj-obj-str',
                ],
                'array' => [
                    'obj-arr-str',
                    'merged-obj-arr-str',
                ],
                'added-string' => 'added-obj-str',
            ],
            'array' => [
                'arr-str',
                (object)[
                    'string' => 'arr-obj-str',
                ],
                [
                    'arr-arr-str',
                ],
                'merged-arr-str',
            ],
            'added-string' => 'added-str',
        ], $merged->jsonSerialize());
    }

    public function testCoercionToBool(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->assertSame(false, $config->get('null', coerce: 'boolean'));
        $this->assertSame(false, $config->get('false', coerce: 'boolean'));
        $this->assertSame(true, $config->get('true', coerce: 'boolean'));
        $this->assertSame(false, $config->get('integer-0', coerce: 'boolean'));
        $this->assertSame(true, $config->get('integer-1', coerce: 'boolean'));
        $this->assertSame(false, $config->get('float-0', coerce: 'boolean'));
        $this->assertSame(true, $config->get('float-1', coerce: 'boolean'));
        $this->assertSame(false, $config->get('string-empty', coerce: 'boolean'));
        $this->assertSame(false, $config->get('string-0', coerce: 'boolean'));
        $this->assertSame(false, $config->get('string-false', coerce: 'boolean'));
        $this->assertSame(true, $config->get('string-1', coerce: 'boolean'));
        $this->assertSame(true, $config->get('string-true', coerce: 'boolean'));
    }

    public function testCoercionToInt(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->assertSame(0, $config->get('null', coerce: 'integer'));
        $this->assertSame(0, $config->get('false', coerce: 'integer'));
        $this->assertSame(1, $config->get('true', coerce: 'integer'));
        $this->assertSame(0, $config->get('integer-0', coerce: 'integer'));
        $this->assertSame(1, $config->get('integer-1', coerce: 'integer'));
        $this->assertSame(0, $config->get('float-0', coerce: 'integer'));
        $this->assertSame(1, $config->get('float-1', coerce: 'integer'));
        $this->assertSame(2, $config->get('float-2', coerce: 'integer'));
        $this->assertSame(0, $config->get('string-0', coerce: 'integer'));
        $this->assertSame(1, $config->get('string-1', coerce: 'integer'));
        $this->assertSame(2, $config->get('string-2', coerce: 'integer'));
    }

    public function testCoercionToDouble(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->assertSame(0.0, $config->get('null', coerce: 'double'));
        $this->assertSame(0.0, $config->get('false', coerce: 'double'));
        $this->assertSame(1.0, $config->get('true', coerce: 'double'));
        $this->assertSame(0.0, $config->get('integer-0', coerce: 'double'));
        $this->assertSame(1.0, $config->get('integer-1', coerce: 'double'));
        $this->assertSame(0.0, $config->get('float-0', coerce: 'double'));
        $this->assertSame(1.0, $config->get('float-1', coerce: 'double'));
        $this->assertSame(2.0, $config->get('float-2', coerce: 'double'));
        $this->assertSame(0.0, $config->get('string-0', coerce: 'double'));
        $this->assertSame(1.0, $config->get('string-1', coerce: 'double'));
        $this->assertSame(2.0, $config->get('string-2', coerce: 'double'));
    }

    public function testCoercionToString(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->assertSame('null', $config->get('null', coerce: 'string'));
        $this->assertSame('false', $config->get('false', coerce: 'string'));
        $this->assertSame('true', $config->get('true', coerce: 'string'));
        $this->assertSame('0', $config->get('integer-0', coerce: 'string'));
        $this->assertSame('1', $config->get('integer-1', coerce: 'string'));
        $this->assertSame('0', $config->get('float-0', coerce: 'string'));
        $this->assertSame('1', $config->get('float-1', coerce: 'string'));
    }

    public function testCoercionToNull(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->assertSame(null, $config->get('null', coerce: 'null'));
        $this->assertSame(null, $config->get('false', coerce: 'null'));
        $this->assertSame(null, $config->get('integer-0', coerce: 'null'));
        $this->assertSame(null, $config->get('float-0', coerce: 'null'));
        $this->assertSame(null, $config->get('string-empty', coerce: 'null'));
        $this->assertSame(null, $config->get('string-null', coerce: 'null'));
    }

    public function testFailedCoercionIntToBool(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce integer 2 to boolean");
        $config->get('integer-2', coerce: 'boolean');
    }

    public function testFailedCoercionStringToBool(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce string 'test' to boolean");
        $config->get('string-test', coerce: 'boolean');
    }

    public function testFailedCoercionArrayToBool(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce array to boolean");
        $config->get('array', coerce: 'boolean');
    }

    public function testFailedCoercionObjectToBool(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce object to boolean");
        $config->get('object', coerce: 'boolean');
    }

    public function testFailedCoercionStringToInt(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce string 'test' to integer");
        $config->get('string-test', coerce: 'integer');
    }

    public function testFailedCoercionArrayToInt(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce array to integer");
        $config->get('array', coerce: 'integer');
    }

    public function testFailedCoercionObjectToInt(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce object to integer");
        $config->get('object', coerce: 'integer');
    }

    public function testFailedCoercionStringToDouble(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce string 'test' to double");
        $config->get('string-test', coerce: 'double');
    }

    public function testFailedCoercionArrayToDouble(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce array to double");
        $config->get('array', coerce: 'double');
    }

    public function testFailedCoercionObjectToDouble(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce object to double");
        $config->get('object', coerce: 'double');
    }

    public function testFailedCoercionArrayToString(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce array to string");
        $config->get('array', coerce: 'string');
    }

    public function testFailedCoercionObjectToString(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce object to string");
        $config->get('object', coerce: 'string');
    }

    public function testFailedCoercionStringToNull(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce string 'test' to null");
        $config->get('string-test', coerce: 'null');
    }

    public function testFailedCoercionIntToNull(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce integer 1 to null");
        $config->get('integer-1', coerce: 'null');
    }

    public function testFailedCoercionDoubleToNull(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce double 1 to null");
        $config->get('float-1', coerce: 'null');
    }

    public function testFailedCoercionTrueToNull(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce boolean 1 to null");
        $config->get('true', coerce: 'null');
    }

    public function testFailedCoercionArrayToNull(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce array to null");
        $config->get('array', coerce: 'null');
    }

    public function testFailedCoercionObjectToNull(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Failed to coerce object to null");
        $config->get('object', coerce: 'null');
    }

    public function testInvalidCoercionType(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Invalid coercion type 'unsupported'");
        $config->get('object', coerce: 'unsupported');
    }
}
