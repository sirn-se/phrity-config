<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class XmlReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['extension_loaded'] = true;
    }

    public function testXmlReader(): void
    {
        $reader = new XmlReader();
        $this->assertInstanceOf(XmlReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration();
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testXmlReaderParse(): void
    {
        $xml = '<a><b><c>C</c></b><d>D1</d><d>D2</d></a>';
        $reader = new XmlReader();
        $config = $reader->createConfiguration(xml: $xml);
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

    public function testXmlReaderClass(): void
    {
        $reader = new XmlReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration();
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testInvalidInput(): void
    {
        $xml = '** invalid **';
        $reader = new XmlReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('XML: Parse error');
        $config = $reader->createConfiguration(xml: $xml);
    }

    public function testMissingDependency(): void
    {
        $GLOBALS['extension_loaded'] = false; // Overload core function

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("Extension 'libxml' not installed, can not read XML.");
        $reader = new XmlReader();
    }
}
