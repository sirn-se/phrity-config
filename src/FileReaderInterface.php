<?php

namespace Phrity\Config;

interface FileReaderInterface extends ReaderInterface
{
    public function createConfiguration(string $path = ''): ConfigurationInterface;
}
