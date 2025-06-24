<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class DataReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['class_exists'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testDataReader(): void
    {
        $reader = new DataReader();
        $this->assertInstanceOf(DataReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration();
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testDataReaderParse(): void
    {
        $json = '{"A": "A", "B": "B"}';
        $reader = new DataReader();
        $config = $reader->createConfiguration(data: ['a' => 'A', 'b' => 'B']);
        $this->assertEquals((object)[
            'a' => 'A',
            'b' => 'B'
        ], $config->jsonSerialize());
    }

    public function testDataReaderClass(): void
    {
        $reader = new DataReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration();
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }
}
