<?php

namespace Phrity\Config;

class DataReader implements ReaderInterface
{
    protected string $class;

    public function __construct(string $class = Configuration::class)
    {
        $this->class = $class;
    }

    public function createConfiguration(object|array|null $data = null): ConfigurationInterface
    {
        return new $this->class($data ?? []);
    }
}
