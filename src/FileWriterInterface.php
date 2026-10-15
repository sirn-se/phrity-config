<?php

namespace Phrity\Config;

interface FileWriterInterface extends WriterInterface
{
    public function writeConfiguration(ConfigurationInterface $configuration, string $path = ''): void;
}
