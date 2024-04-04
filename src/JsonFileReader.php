<?php

namespace Phrity\Config;

class JsonFileReader extends JsonReader implements ReaderInterface
{
    private string $prefix;

    public function __construct(string $class = Configuration::class, string $prefix = '')
    {
        parent::__construct($class);
        $this->prefix = $prefix;
    }

    public function createConfiguration(string $path = 'config.json'): ConfigurationInterface
    {
        $file = "{$this->prefix}{$path}";
        if (!is_readable($file)) {
            throw new ReaderException("File '{$file}' can not be read.");
        }
        return parent::createConfiguration(file_get_contents($file));
    }
}
