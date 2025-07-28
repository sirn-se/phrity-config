<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class ConfigurationFactoryTest extends TestCase
{
    public function testConfigurationFactory(): void
    {
        $factory = new ConfigurationFactory();
        $this->assertInstanceOf(ConfigurationFactory::class, $factory);
    }

    public function testFromData(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromData(['a' => 23]);
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromJson(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromJson('{}');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromJsonFile(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromJsonFile(path: __DIR__ . '/../fixtures/valid.json');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromYaml(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromYaml('b: 66');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromYamlFile(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromYamlFile(path: __DIR__ . '/../fixtures/valid.yaml');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromNeon(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromNeon('b: 66');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromNeonFile(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromNeonFile(path: __DIR__ . '/../fixtures/valid.neon');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromEnv(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromEnv();
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromEnvFile(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromEnvFile(path: __DIR__ . '/../fixtures/valid.env');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFactoryClass(): void
    {
        $factory = new ConfigurationFactory(class: TestConfiguration::class);
        $config = $factory->fromJson('{}');
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testOptional(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromJsonFile(path: 'no/file/here', optional: true);
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
        $config = $factory->fromYamlFile(path: 'no/file/here', optional: true);
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
        $config = $factory->fromEnvFile(path: 'no/file/here', optional: true);
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testMerge(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->merge(
            $factory->fromJson('{}'),
            $factory->fromJsonFile(path: __DIR__ . '/../fixtures/valid.json'),
            $factory->fromYamlFile(path: __DIR__ . '/../fixtures/valid.yaml'),
            $factory->fromEnv(),
        );
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }
}
