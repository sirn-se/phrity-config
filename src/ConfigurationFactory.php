<?php

namespace Phrity\Config;

class ConfigurationFactory
{
    private string $class;


    /* ---------- Public methods ----------------------------------------------------------------------------------- */

    public function __construct(string $class = Configuration::class)
    {
        $this->class = $class;
    }

    public function fromJson(string $json): ConfigurationInterface
    {
        $reader = new JsonReader(class: $this->class);
        return $reader->createConfiguration(json: $json);
    }

    public function fromJsonFile(string $path): ConfigurationInterface
    {
        $reader = new JsonFileReader(class: $this->class);
        return $reader->createConfiguration(path: $path);
    }

    public function fromYamlFile(string $path): ConfigurationInterface
    {
        $reader = new YamlFileReader(class: $this->class);
        return $reader->createConfiguration(path: $path);
    }

    public function fromEnv(string|null $separator = null, array|null $match = null): ConfigurationInterface
    {
        $reader = new EnvReader(class: $this->class, separator: $separator);
        return $reader->createConfiguration(match: $match);
    }

    public function merge(ConfigurationInterface ...$configurations): ConfigurationInterface
    {
        return array_reduce($configurations, function (ConfigurationInterface $carry, ConfigurationInterface $item) {
            return $carry->merge($item);
        }, new $this->class((object)[]));
    }
}
