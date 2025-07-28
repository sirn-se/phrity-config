<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;
use Symfony\Component\Neon\Exception\ParseException;

class NeonFileReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['class_exists'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testNeonFileReader(): void
    {
        $reader = new NeonFileReader();
        $this->assertInstanceOf(NeonFileReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.neon');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testNeonFileReaderParse(): void
    {
        $reader = new NeonFileReader();
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.neon');
        $this->assertEquals((object)[
            'a' => (object)[
                'a' => 1,
                'b' => 2,
            ],
            'b' => 66,
        ], $config->jsonSerialize());
    }

    public function testNeonFileReaderPrefix(): void
    {
        $reader = new NeonFileReader(prefix: __DIR__ . '/../fixtures/');
        $config = $reader->createConfiguration(path: 'valid.neon');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testNeonFileReaderClass(): void
    {
        $reader = new NeonFileReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.neon');
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testNeonFileOptional(): void
    {
        $reader = new NeonFileReader(optional: true);
        $config = $reader->createConfiguration(path: 'no/file/here');
        $this->assertEquals((object)[], $config->jsonSerialize());
    }

    public function testFileNotFound(): void
    {
        $reader = new NeonFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("File 'no/file/here' not found.");
        $config = $reader->createConfiguration(path: 'no/file/here');
    }

    public function testFileNotReadable(): void
    {
        $GLOBALS['is_readable'] = false; // Overload core function

        $reader = new NeonFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("can not be read.");
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.neon');
    }

    public function testInvalidInput(): void
    {
        $reader = new NeonFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('NEON: Bad indentation on line 2');
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/invalid.neon');
    }

    public function testMissingDependency(): void
    {
        $GLOBALS['class_exists'] = false; // Overload core function

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("Dependency 'nette/neon' not installed, can not read NEON file.");
        $reader = new NeonFileReader();
    }
}
