<?php

declare(strict_types=1);

namespace Phrity\Config;

use PHPUnit\Framework\TestCase;
use Phrity\Config\Test\TestConfiguration;

class JsonFileWriterTest extends TestCase
{
    public function setUp(): void
    {
        $GLOBALS['is_dir'] = null;
        $GLOBALS['mkdir'] = null;
        $GLOBALS['file_put_contents'] = null;
    }

    public function tearDown(): void
    {
        if (file_exists($this->testfile())) {
            unlink($this->testfile());
        }
    }

    public function testJsonFileWriter(): void
    {
        $writer = new JsonFileWriter();
        $this->assertInstanceOf(JsonFileWriter::class, $writer);
        $this->assertInstanceOf(FileWriterInterface::class, $writer);
        $this->assertInstanceOf(WriterInterface::class, $writer);

        $configuration = new Configuration(['a' => 1, 'b' => 2]);
        $writer->writeConfiguration($configuration, $this->testfile());

        $json = file_get_contents($this->testfile());
        $this->assertEquals("{\"a\":1,\"b\":2}", $json);
    }

    public function testJsonFileWriterPretty(): void
    {
        $writer = new JsonFileWriter(pretty: true);
        $configuration = new Configuration(['a' => 1, 'b' => 2]);
        $writer->writeConfiguration($configuration, $this->testfile());

        $json = file_get_contents($this->testfile());
        $this->assertEquals("{\n    \"a\": 1,\n    \"b\": 2\n}", $json);
    }

    public function testDirectoryNotCreated(): void
    {
        $GLOBALS['is_dir'] = false; // Overload core function
        $GLOBALS['mkdir'] = false; // Overload core function

        $writer = new JsonFileWriter();
        $configuration = new Configuration(['a' => 1, 'b' => 2]);

        $this->expectException(WriterException::class);
        $this->expectExceptionMessage("can not be written.");
        $writer->writeConfiguration($configuration, $this->testfile());
    }

    public function testFileNotWritable(): void
    {
        $GLOBALS['file_put_contents'] = false; // Overload core function

        $writer = new JsonFileWriter();
        $configuration = new Configuration(['a' => 1, 'b' => 2]);

        $this->expectException(WriterException::class);
        $this->expectExceptionMessage("can not be written.");
        $writer->writeConfiguration($configuration, $this->testfile());
    }

    private function testfile(): string
    {
        return sys_get_temp_dir() . "/phrity-config-test/test.json";
    }
}
