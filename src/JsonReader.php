<?php

namespace Phrity\Config;

class JsonReader implements ReaderInterface
{
    private string $class;

    public function __construct(string $class = Configuration::class)
    {
        if (!function_exists('json_decode')) {
            throw new ReaderException("Extension 'ext-json' not installed, can not read JSON file.");
        }
        $this->class = $class;
    }

    public function createConfiguration(string $json = '{}'): ConfigurationInterface
    {
        $data = json_decode($json, true, JSON_THROW_ON_ERROR);
        return new $this->class($data);
    }
}
