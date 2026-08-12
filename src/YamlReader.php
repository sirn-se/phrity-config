<?php

namespace Phrity\Config;

/**
 * @template T of ConfigurationInterface
 */
class YamlReader implements ReaderInterface
{
    use YamlTrait;

    /** @var class-string<T> $class */
    protected string $class;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        string $class = Configuration::class,
    ) {
        $this->yamlInstalled();
        $this->class = $class;
    }

    /**
     * @return T
     */
    public function createConfiguration(
        string $yaml = '{}',
    ): ConfigurationInterface {
        return new $this->class($this->yamlDecode($yaml));
    }
}
