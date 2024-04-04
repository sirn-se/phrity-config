<?php

namespace Phrity\Config;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
