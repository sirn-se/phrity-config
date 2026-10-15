<?php

declare(strict_types=1);

namespace Phrity\Config;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Phrity\Util\Transformer\Type;
use Psr\Container\ContainerInterface;

class WritableConfigurationTest extends TestCase
{
    public function testWritableConfiguration(): void
    {
        $config = new WritableConfiguration();
        $this->assertInstanceOf(ConfigurationInterface::class, $config);
        $this->assertInstanceOf(ContainerInterface::class, $config);
        $this->assertInstanceOf(JsonSerializable::class, $config);
    }

    public function testWith(): void
    {
        $reader = new JsonFileReader(class: WritableConfiguration::class);
        $config_orig = $reader->createConfiguration(__DIR__ . '/../fixtures/valid.json');

        $config_new = $config_orig
            ->with('A/a', 3)
            ->with('B', [45])
            ->with('C/a', 56);

        $this->assertEquals((object)[
            'a' => (object)[
                'a' => 1,
                'b' => 2,
            ],
            'b' => [
                23,
            ]
        ], $config_orig->get('/'));
        $this->assertEquals((object)[
            'a' => (object)[
                'a' => 3,
                'b' => 2,
            ],
            'b' => [
                45,
            ],
            'c' => [
                'a' => 56,
            ]
        ], $config_new->get('/'));
    }

    public function testSet(): void
    {
        $reader = new JsonFileReader(class: WritableConfiguration::class);
        $config = $reader->createConfiguration(__DIR__ . '/../fixtures/valid.json');

        $config->set('A/a', 3);
        $config->set('B', [45]);
        $config->set('C/a', 56);

        $this->assertEquals((object)[
            'a' => (object)[
                'a' => 3,
                'b' => 2,
            ],
            'b' => [
                45,
            ],
            'c' => [
                'a' => 56,
            ]
        ], $config->get('/'));
    }
}
