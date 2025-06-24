<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlFileReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['class_exists'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testYamlFileReader(): void
    {
        $reader = new YamlFileReader();
        $this->assertInstanceOf(YamlFileReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.yaml');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testYamlFileReaderParse(): void
    {
        $reader = new YamlFileReader();
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.yaml');
        $this->assertEquals((object)[
            'a' => (object)[
                'a' => 1,
                'b' => 2,
            ],
            'b' => 66,
        ], $config->jsonSerialize());
    }

    public function testYamlFileReaderPrefix(): void
    {
        $reader = new YamlFileReader(prefix: __DIR__ . '/../fixtures/');
        $config = $reader->createConfiguration(path: 'valid.yaml');
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testYamlFileReaderClass(): void
    {
        $reader = new YamlFileReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.yaml');
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testYamlFileOptional(): void
    {
        $reader = new YamlFileReader(optional: true);
        $config = $reader->createConfiguration(path: 'no/file/here');
        $this->assertEquals((object)[], $config->jsonSerialize());
    }

    public function testFileNotFound(): void
    {
        $reader = new YamlFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("File 'no/file/here' not found.");
        $config = $reader->createConfiguration(path: 'no/file/here');
    }

    public function testFileNotReadable(): void
    {
        $GLOBALS['is_readable'] = false; // Overload core function

        $reader = new YamlFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("can not be read.");
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/valid.yaml');
    }

    public function testInvalidInput(): void
    {
        $reader = new YamlFileReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('YAML: A colon cannot be used in an unquoted mapping value');
        $config = $reader->createConfiguration(path: __DIR__ . '/../fixtures/invalid.yaml');
    }

    public function testMissingDependency(): void
    {
        $GLOBALS['class_exists'] = false; // Overload core function

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("Dependency 'symfony/yaml' not installed, can not read YAML file.");
        $reader = new YamlFileReader();
    }
}
