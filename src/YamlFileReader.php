<?php

namespace Phrity\Config;

class YamlFileReader extends YamlReader implements ReaderInterface
{
    use FileTrait;

    public function __construct(
        string $class = Configuration::class,
        string $prefix = '',
        bool $optional = false,
    ) {
        parent::__construct($class);
        $this->prefix = $prefix;
        $this->optional = $optional;
    }

    public function createConfiguration(string $path = 'config.yaml'): ConfigurationInterface
    {
        $content = $this->readFile($path);
        if (is_null($content)) {
            return new $this->class();
        }
        return parent::createConfiguration($content);
    }
}
