<?php

namespace Phrity\Config;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlReader implements ReaderInterface
{
    protected string $class;
    protected Parser $parser;

    public function __construct(string $class = Configuration::class)
    {
        if (!class_exists(Parser::class)) {
            throw new ReaderException("Dependency 'symfony/yaml' not installed, can not read YAML file.");
        }
        $this->class = $class;
        $this->parser = new Parser();
    }

    public function createConfiguration(string $yaml = '{}'): ConfigurationInterface
    {
        try {
            $data = $this->parser->parse($yaml);
            return new $this->class($data);
        } catch (ParseException $e) {
            throw new ReaderException("YAML: {$e->getMessage()}");
        }
    }
}
