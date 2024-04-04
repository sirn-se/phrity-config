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

    public function testFromJson(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromJson('{}');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromJsonFile(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromJsonFile(path: __DIR__ . '/fixtures/valid.json');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromYamlFile(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromYamlFile(path: __DIR__ . '/fixtures/valid.yaml');
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFromEnv(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->fromEnv();
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }

    public function testFactoryClass(): void
    {
        $factory = new ConfigurationFactory(class: TestConfiguration::class);
        $config = $factory->fromJson('{}');
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testMerge(): void
    {
        $factory = new ConfigurationFactory();
        $config = $factory->merge(
            $factory->fromJson('{}'),
            $factory->fromJsonFile(path: __DIR__ . '/fixtures/valid.json'),
            $factory->fromYamlFile(path: __DIR__ . '/fixtures/valid.yaml'),
            $factory->fromEnv(),
        );
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
    }
}
