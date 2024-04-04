<?php

namespace Phrity\Config;

class JsonFileReader implements ReaderInterface
{
    private string $class;
    private string $prefix;

    public function __construct(string $class = Configuration::class, string $prefix = '')
    {
        if (!function_exists('json_decode')) {
            throw new ReaderException("Extension 'ext-json' not installed, can not read JSON file.");
        }
        $this->class = $class;
        $this->prefix = $prefix;
    }

    public function createConfiguration(string $path = 'config.json'): ConfigurationInterface
    {
        $file = "{$this->prefix}{$path}";
        if (!is_readable($file)) {
            throw new ReaderException("File '{$file}' can not be read.");
        }
        $data = json_decode(file_get_contents($file), true, JSON_THROW_ON_ERROR);
        return new $this->class($data);
    }
}
