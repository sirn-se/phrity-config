<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class XmlFileReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['extension_loaded'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testXmlFileReader(): void
    {
        $reader = new XmlFileReader();
        $this->assertInstanceOf(XmlFileReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.xml');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testNeonFileReaderParse(): void
    {
        $reader = new XmlFileReader();
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.xml');
        $this->assertEquals((object)[
            'a' => (object)[
                'b' => (object)[
                    'c' => 'C',
                ],
                'd' => [
                    'D1',
                    'D2',
                ],
            ],
        ], $config->jsonSerialize());
    }

    public function testXmlFileReaderPrefix(): void
    {
        $reader = new XmlFileReader(prefix: __DIR__ . '/../fixtures/');
        $config = $reader->createConfiguration(path: 'valid.xml');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testXmlFileReaderClass(): void
    {
        $reader = new XmlFileReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.xml');
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testXmlFileOptional(): void
    {
        $reader = new XmlFileReader(optional: true);
        $config = $reader->createConfiguration(path: 'no/file/here');
        $this->assertEquals((object)[], $config->jsonSerialize());
    }

    public function testFileNotFound(): void
    {
        $reader = new XmlFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("File 'no/file/here' not found.");
        $config = $reader->createConfiguration(path: 'no/file/here');
    }

    public function testFileNotReadable(): void
    {
        $GLOBALS['is_readable'] = false; // Overload core function

        $reader = new XmlFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("can not be read.");
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.xml');
    }

    public function testInvalidInput(): void
    {
        $reader = new XmlFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('XML: Parse error');
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/invalid.xml');
    }

    public function testMissingDependency(): void
    {
        $GLOBALS['extension_loaded'] = false; // Overload core function

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("Extension 'libxml' not installed, can not read XML.");
        $reader = new XmlFileReader();
    }
}
