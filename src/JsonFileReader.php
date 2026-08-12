<?php

namespace Phrity\Config;

/**
 * @template T of ConfigurationInterface
 */
class JsonFileReader implements FileReaderInterface
{
    use FileTrait;
    use JsonTrait;

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
        $this->class = $class;
        $this->prefix = $prefix;
        $this->optional = $optional;
    }

    /**
     * @return T
     */
    public function createConfiguration(
        string $path = 'config.json',
    ): ConfigurationInterface {
        $json = $this->readFile($path);
        if (is_null($json)) {
            return new $this->class();
        }
        return new $this->class($this->jsonDecode($json));
    }
}
