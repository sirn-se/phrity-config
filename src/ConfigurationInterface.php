<?php

namespace Phrity\Config;

use JsonSerializable;
use Psr\Container\ContainerInterface;

interface ConfigurationInterface extends ContainerInterface, JsonSerializable
{
    /**
     * @param object|array<mixed> $config
     */
    public function __construct(object|array $config);
    public function merge(ConfigurationInterface $config): self;
    public function get(string $id, mixed ...$opt): mixed;
}
