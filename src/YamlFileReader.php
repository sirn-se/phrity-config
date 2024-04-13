<?php

namespace Phrity\Config;

class YamlFileReader extends YamlReader implements ReaderInterface
{
    use FileTrait;

    public function __construct(string $class = Configuration::class, string $prefix = '')
    {
        parent::__construct($class);
        $this->prefix = $prefix;
    }

    public function createConfiguration(string $path = 'config.yaml'): ConfigurationInterface
    {
        $content = $this->readFile($path);
        return parent::createConfiguration($content);
    }
}
