<?php

namespace Phrity\Config;

interface ReaderInterface
{
    public function createConfiguration(): ConfigurationInterface;
}
