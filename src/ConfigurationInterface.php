<?php

namespace Phrity\Config;

use Psr\Container\ContainerInterface;

interface ConfigurationInterface extends ContainerInterface
{
    public function __construct(object|array $config);
}
