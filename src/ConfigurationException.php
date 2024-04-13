<?php

namespace Phrity\Config;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ConfigurationException extends RuntimeException implements ContainerExceptionInterface
{
}
