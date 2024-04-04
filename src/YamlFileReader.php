<?php

namespace Phrity\Config;

use Symfony\Component\Yaml\Yaml;

class YamlFileReader implements ReaderInterface
{
    private string $class;
    private string $prefix;

    public function __construct(string $class = Configuration::class, string $prefix = '')
    {
        if (!class_exists(Yaml::class)) {
            throw new ReaderException("Dependency 'symfony/yaml' not installed, can not read YAML file.");
        }
        $this->class = $class;
        $this->prefix = $prefix;
    }

    public function createConfiguration(string $path = 'config.yaml'): ConfigurationInterface
    {
        $file = "{$this->prefix}{$path}";
        $data = Yaml::parseFile($file);
        return new $this->class($data);
    }
}
