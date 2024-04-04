<?php

namespace Phrity\Config;

class JsonReader implements ReaderInterface
{
    private string $class;

    public function __construct(string $class = Configuration::class)
    {
        $this->class = $class;
    }

    public function createConfiguration(string $json = '{}'): ConfigurationInterface
    {
        $data = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        return new $this->class($data);
    }
}
