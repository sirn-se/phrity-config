<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class EnvFileReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['class_exists'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testEnvFileReader(): void
    {
        $reader = new EnvFileReader();
        $this->assertInstanceOf(EnvFileReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/valid.env');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testEnvFileReaderParse(): void
    {
        $reader = new EnvFileReader();
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/valid.env');
        $this->assertEquals((object)[
            'test' => '0',
            'test_a' => 'A',
            'test_b' => 'B',
        ], $config->jsonSerialize());
    }

    public function testEnvFileReaderPrefix(): void
    {
        $reader = new EnvFileReader(prefix: __DIR__ . '/fixtures/');
        $config = $reader->createConfiguration(path: 'valid.env');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testEnvFileReaderClass(): void
    {
        $reader = new EnvFileReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/valid.env');
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testEnvFileReaderMatch(): void
    {
        $reader = new EnvFileReader();
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/valid.env', match: ['TEST_A', 'TEST_B']);
        $this->assertEquals((object)[
            'test_a' => 'A',
            'test_b' => 'B'
        ], $config->jsonSerialize());
    }

    public function testEnvFileReaderPath(): void
    {
        $reader = new EnvFileReader(separator: '_');
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/valid.env');
        $this->assertEquals((object)[
            'test' => (object)[
                '_' => '0',
                'a' => 'A',
                'b' => 'B',
            ],
        ], $config->jsonSerialize());
    }

    public function testFileNotFound(): void
    {
        $reader = new EnvFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("File 'no/file/here' not found.");
        $config = $reader->createConfiguration(path: 'no/file/here');
    }

    public function testFileNotReadable(): void
    {
        $GLOBALS['is_readable'] = false;

        $reader = new EnvFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("can not be read.");
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/invalid.env');
    }

    public function testInvalidInput(): void
    {
        $reader = new EnvFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('ENV: Invalid character in variable name');
        $config = $reader->createConfiguration(path: __DIR__ . '/fixtures/invalid.env');
    }

    public function testMissingDependency(): void
    {
        $GLOBALS['class_exists'] = false; // Overload core function

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("Dependency 'symfony/dotenv' not installed, can not read .env file.");
        $reader = new EnvFileReader();
    }
}
