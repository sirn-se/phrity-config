<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class NeonReaderTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['class_exists'] = true;
        $GLOBALS['is_readable'] = true;
    }

    public function testNeonReader(): void
    {
        $reader = new NeonReader();
        $this->assertInstanceOf(NeonReader::class, $reader);
        $this->assertInstanceOf(ReaderInterface::class, $reader);

        $config = $reader->createConfiguration();
        $this->assertInstanceOf(Configuration::class, $config);
    }

    public function testNeonReaderParse(): void
    {
        $neon = 'b: 66';
        $reader = new NeonReader();
        $config = $reader->createConfiguration(neon: $neon);
        $this->assertEquals((object)[
            'b' => 66,
        ], $config->jsonSerialize());
    }

    public function testNeonReaderClass(): void
    {
        $reader = new NeonReader(class: TestConfiguration::class);
        $config = $reader->createConfiguration();
        $this->assertInstanceOf(TestConfiguration::class, $config);
    }

    public function testInvalidInput(): void
    {
        $neon = '** invalid **';
        $reader = new NeonReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('NEON: Invalid input');
        $config = $reader->createConfiguration(neon: $neon);
    }

    public function testInvalidFormat(): void
    {
        $neon = "not\nallowed";
        $reader = new NeonReader();
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("NEON: Unexpected 'allowed' on line 2");
        $config = $reader->createConfiguration(neon: $neon);
    }

    public function testMissingDependency(): void
    {
        $GLOBALS['class_exists'] = false; // Overload core function

        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage("Dependency 'nette/neon' not installed, can not read NEON file.");
        $reader = new NeonReader();
    }
}
