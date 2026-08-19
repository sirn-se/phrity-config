<?php

namespace Phrity\Config;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * @template T of ConfigurationInterface
 */
class YamlFileReader implements FileReaderInterface
{
    use FileTrait;
    use YamlTrait;

    /** @var class-string<T> $class */
    protected string $class;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        string $class = Configuration::class,
        string $prefix = '',
        bool $optional = false,
    ) {
        $this->yamlInstalled();
        $this->class = $class;
        $this->prefix = $prefix;
        $this->optional = $optional;
    }

    /**
     * @return T
     */
    public function createConfiguration(
        string $path = 'config.yaml'
    ): ConfigurationInterface {
        $yaml = $this->readFile($path);
        if (is_null($yaml)) {
            return new $this->class();
        }
        return new $this->class($this->yamlDecode($yaml));
    }
}
