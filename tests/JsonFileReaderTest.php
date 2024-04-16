<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class JsonFileReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['class_exists'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testJsonFileReader(): void
    {
        $reader = new JsonFileReader();
        $this->assertInstanceOf(JsonFileReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/valid.json');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testJsonFileReaderParse(): void
    {
        $reader = new JsonFileReader();
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/valid.json');
        $this->assertEquals((object)[
            'a' => (object)[
                'a' => 1,
                'b' => 2,
            ],
            'b' => [
                23.
            ],
        ], $config->jsonSerialize());
    }

    public function testJsonFileReaderPrefix(): void
    {
        $reader = new JsonFileReader(prefix: __DIR__ . '/fixtures/');
        $config = $reader->createConfiguration(path: 'valid.json');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testJsonFileReaderClass(): void
    {
        $reader = new JsonFileReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/valid.json');
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testJsonFileOptional(): void
    {
        $reader = new JsonFileReader(optional: true);
        $config = $reader->createConfiguration(path: 'no/file/here');
        $this->assertEquals((object)[], $config->jsonSerialize());
    }

    public function testFileNotFound(): void
    {
        $reader = new JsonFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("File 'no/file/here' not found.");
        $config = $reader->createConfiguration(path: 'no/file/here');
    }

    public function testFileNotReadable(): void
    {
        $GLOBALS['is_readable'] = false; // Overload core function

        $reader = new JsonFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("can not be read.");
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/valid.json');
    }

    public function testInvalidInput(): void
    {
        $reader = new JsonFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('JSON: Syntax error');
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/invalid.json');
    }
}
