<?php

declare(strict_types=1);

namespace Phrity\Config;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Phrity\Util\Transformer\Type;
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
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/complex.json');

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
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/complex.json');

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
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/complex.json');

        $this->assertEquals('No', $config->get('no', default: 'No'));
        $this->assertEquals('False', $config->get('object/false', default: 'False'));
        $this->assertEquals('4', $config->get('array/4', default: '4'));
    }

    public function testNotFound(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/complex.json');

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("No configuration entry with id 'no'.");
        $config->get('no');
    }

    public function testJsonSerialize(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/valid.json');

        $this->assertEquals((object)[
            'a' => (object)['a' => 1, 'b' => 2],
            'b' => [23],
        ], $config->jsonSerialize());
    }

    public function testMerge(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/complex.json');
        $extend = $reader->createConfiguration(__DIR__ . '/../fixtures/merge.json');
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
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/coercion.json');
        $this->assertSame(false, $config->get('null', coerce: Type::BOOLEAN));
        $this->assertSame(false, $config->get('false', coerce: Type::BOOLEAN));
        $this->assertSame(true, $config->get('true', coerce: Type::BOOLEAN));
        $this->assertSame(false, $config->get('integer-0', coerce: Type::BOOLEAN));
        $this->assertSame(true, $config->get('integer-1', coerce: Type::BOOLEAN));
        $this->assertSame(false, $config->get('float-0', coerce: Type::BOOLEAN));
        $this->assertSame(true, $config->get('float-1', coerce: Type::BOOLEAN));
        $this->assertSame(false, $config->get('string-empty', coerce: Type::BOOLEAN));
        $this->assertSame(false, $config->get('string-0', coerce: Type::BOOLEAN));
        $this->assertSame(false, $config->get('string-false', coerce: Type::BOOLEAN));
        $this->assertSame(true, $config->get('string-1', coerce: Type::BOOLEAN));
        $this->assertSame(true, $config->get('string-true', coerce: Type::BOOLEAN));
        $this->assertSame(true, $config->get('integer-2', coerce: Type::BOOLEAN));
        $this->assertSame(true, $config->get('string-test', coerce: Type::BOOLEAN));
        $this->assertSame(true, $config->get('array', coerce: Type::BOOLEAN));
        $this->assertSame(true, $config->get('object', coerce: Type::BOOLEAN));
    }

    public function testCoercionToInt(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/coercion.json');
        $this->assertSame(0, $config->get('null', coerce: Type::INTEGER));
        $this->assertSame(0, $config->get('false', coerce: Type::INTEGER));
        $this->assertSame(1, $config->get('true', coerce: Type::INTEGER));
        $this->assertSame(0, $config->get('integer-0', coerce: Type::INTEGER));
        $this->assertSame(1, $config->get('integer-1', coerce: Type::INTEGER));
        $this->assertSame(0, $config->get('float-0', coerce: Type::INTEGER));
        $this->assertSame(1, $config->get('float-1', coerce: Type::INTEGER));
        $this->assertSame(2, $config->get('float-2', coerce: Type::INTEGER));
        $this->assertSame(0, $config->get('string-0', coerce: Type::INTEGER));
        $this->assertSame(1, $config->get('string-1', coerce: Type::INTEGER));
        $this->assertSame(2, $config->get('string-2', coerce: Type::INTEGER));
        $this->assertSame(0, $config->get('string-test', coerce: Type::INTEGER));
        $this->assertSame(1, $config->get('array', coerce: Type::INTEGER));
        $this->assertSame(1, $config->get('object', coerce: Type::INTEGER));
    }

    public function testCoercionToDouble(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/coercion.json');
        $this->assertSame(0.0, $config->get('null', coerce: Type::NUMBER));
        $this->assertSame(0.0, $config->get('false', coerce: Type::NUMBER));
        $this->assertSame(1.0, $config->get('true', coerce: Type::NUMBER));
        $this->assertSame(0.0, $config->get('integer-0', coerce: Type::NUMBER));
        $this->assertSame(1.0, $config->get('integer-1', coerce: Type::NUMBER));
        $this->assertSame(0.0, $config->get('float-0', coerce: Type::NUMBER));
        $this->assertSame(1.0, $config->get('float-1', coerce: Type::NUMBER));
        $this->assertSame(2.0, $config->get('float-2', coerce: Type::NUMBER));
        $this->assertSame(0.0, $config->get('string-0', coerce: Type::NUMBER));
        $this->assertSame(1.0, $config->get('string-1', coerce: Type::NUMBER));
        $this->assertSame(2.0, $config->get('string-2', coerce: Type::NUMBER));
        $this->assertSame(0.0, $config->get('string-test', coerce: Type::NUMBER));
        $this->assertSame(1.0, $config->get('array', coerce: Type::NUMBER));
        $this->assertSame(1.0, $config->get('object', coerce: Type::NUMBER));
    }

    public function testCoercionToString(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/coercion.json');
        $this->assertSame('null', $config->get('null', coerce: Type::STRING));
        $this->assertSame('false', $config->get('false', coerce: Type::STRING));
        $this->assertSame('true', $config->get('true', coerce: Type::STRING));
        $this->assertSame('0', $config->get('integer-0', coerce: Type::STRING));
        $this->assertSame('1', $config->get('integer-1', coerce: Type::STRING));
        $this->assertSame('0', $config->get('float-0', coerce: Type::STRING));
        $this->assertSame('1', $config->get('float-1', coerce: Type::STRING));
        $this->assertSame('array', $config->get('array', coerce: Type::STRING));
        $this->assertSame('stdClass', $config->get('object', coerce: Type::STRING));
    }

    public function testCoercionToNull(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/coercion.json');
        $this->assertSame(null, $config->get('null', coerce: Type::NULL));
        $this->assertSame(null, $config->get('false', coerce: Type::NULL));
        $this->assertSame(null, $config->get('integer-0', coerce: Type::NULL));
        $this->assertSame(null, $config->get('float-0', coerce: Type::NULL));
        $this->assertSame(null, $config->get('string-empty', coerce: Type::NULL));
        $this->assertSame(null, $config->get('string-null', coerce: Type::NULL));
        $this->assertSame(null, $config->get('string-test', coerce: Type::NULL));
        $this->assertSame(null, $config->get('integer-1', coerce: Type::NULL));
        $this->assertSame(null, $config->get('float-1', coerce: Type::NULL));
        $this->assertSame(null, $config->get('true', coerce: Type::NULL));
        $this->assertSame(null, $config->get('array', coerce: Type::NULL));
        $this->assertSame(null, $config->get('object', coerce: Type::NULL));
    }

    public function testInvalidCoercionType(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/coercion.json');
        $this->expectException(CoercionException::class);
        $this->expectExceptionMessage("Invalid coercion type 'unsupported'");
        $config->get('object', coerce: 'unsupported');
    }
}
