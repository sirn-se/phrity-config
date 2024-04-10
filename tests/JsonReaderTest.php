<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class JsonReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['class_exists'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testJsonReader(): void
    {
        $reader = new JsonReader();
        $this->assertInstanceOf(JsonReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration();
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testJsonReaderParse(): void
    {
        $json = '{"A": "A", "B": "B"}';
        $reader = new JsonReader();
        $config = $reader->createConfiguration(json: $json);
        $this->assertEquals((object)[
            'a' => 'A',
            'b' => 'B'
        ], $config->jsonSerialize());
    }

    public function testJsonReaderClass(): void
    {
        $reader = new JsonReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration();
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testInvalidInput(): void
    {
        $json = '** invalid **';
        $reader = new JsonReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('JSON: Syntax error');
        $config = $reader->createConfiguration(json: $json);
    }
}
