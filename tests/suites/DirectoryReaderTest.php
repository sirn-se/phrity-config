<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class DirectoryReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['class_exists'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testDirectoryReader(): void
    {
        $reader = new DirectoryReader();
        $this->assertInstanceOf(DirectoryReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.json');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testDirectoryReaderParse(): void
    {
        $reader = new DirectoryReader();
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/{valid.*}');
        $this->assertEquals((object)[
            'a' => (object)[
                'a' => 1,
                'b' => 2,
            ],
            'b' => 66,
        ], $config->jsonSerialize());
    }

    public function ttestDirectoryReaderClass(): void
    {
        $reader = new DirectoryReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.json');
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }


    public function testDirectoryNotReadable(): void
    {
        $reader = new DirectoryReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("Could not read pattern 'no/file/here'.");
        $config = $reader->createConfiguration(path: 'no/file/here');
    }

    public function testDirectoryFileNotReadable(): void
    {
        $GLOBALS['is_readable'] = false; // Overload core function

        $reader = new DirectoryReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("can not be read.");
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.json');
    }

    public function testInvalidInput(): void
    {
        $reader = new DirectoryReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('JSON: Syntax error');
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/invalid.json');
    }
}
