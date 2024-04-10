<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class YamlReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['class_exists'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testYamlReader(): void
    {
        $reader = new YamlReader();
        $this->assertInstanceOf(YamlReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration();
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testYamlReaderParse(): void
    {
        $yaml = 'b: 66';
        $reader = new YamlReader();
        $config = $reader->createConfiguration(yaml: $yaml);
        $this->assertEquals((object)[
            'b' => 66,
        ], $config->jsonSerialize());
    }

    public function testYamlReaderClass(): void
    {
        $reader = new YamlReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration();
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testInvalidInput(): void
    {
        $yaml = '** invalid **';
        $reader = new YamlReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('YAML: Reference');
        $config = $reader->createConfiguration(yaml: $yaml);
    }

    public function testMissingDependency(): void
    {
        $GLOBALS['class_exists'] = false; // Overload core function

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("Dependency 'symfony/yaml' not installed, can not read YAML file.");
        $reader = new YamlReader();
    }
}
