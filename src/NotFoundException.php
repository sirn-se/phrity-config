<?php

namespace Phrity\Config;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ConfigurationException implements NotFoundExceptionInterface
{
}
