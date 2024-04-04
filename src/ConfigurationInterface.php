<?php

namespace Phrity\Config;

use JsonSerializable;
use Psr\Container\ContainerInterface;

interface ConfigurationInterface extends ContainerInterface, JsonSerializable
{
    public function __construct(object|array $config);
    public function merge(ConfigurationInterface $config): self;
}
