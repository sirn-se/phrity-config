<?php

namespace Phrity\Config;

interface WriterInterface
{
    public function writeConfiguration(ConfigurationInterface $configuration): void;
}
