<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class EnvReaderTest extends TestCase
{
    public function testEnvReader(): void
    {
        $reader = new EnvReader();
        $this->assertInstanceOf(EnvReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration();
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testEnvReaderMatch(): void
    {
        putenv('TEST_A=A');
        putenv('TEST_B=B');
        $reader = new EnvReader();
        $config = $reader->createConfiguration(match: ['TEST_A', 'TEST_B']);
        $this->assertEquals((object)[
            'test_a' => 'A',
            'test_b' => 'B'
        ], $config->jsonSerialize());
    }

    public function testEnvReaderPath(): void
    {
        putenv('TEST=0');
        putenv('TEST_A=A');
        putenv('TEST_B=B');
        $reader = new EnvReader(separator: '_');
        $config = $reader->createConfiguration(match: ['TEST', 'TEST_A', 'TEST_B']);
        $this->assertEquals((object)[
            'test' => (object)[
                '_' => '0',
                'a' => 'A',
                'b' => 'B',
            ],
        ], $config->jsonSerialize());
    }

    public function testEnvReaderClass(): void
    {
        $reader = new EnvReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration(match: ['TEST_A', 'TEST_B']);
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }
}
