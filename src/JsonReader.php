<?php

namespace Phrity\Config;

use JsonException;

/**
 * @template T of ConfigurationInterface
 */
class JsonReader implements ReaderInterface
{
    use JsonTrait;

    /** @var class-string<T> $class */
    protected string $class;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        string $class = Configuration::class,
    ) {
        $this->class = $class;
    }

    /**
     * @return T
     */
    public function createConfiguration(
        string $json = '{}',
    ): ConfigurationInterface {
        return new $this->class($this->jsonDecode($json));
    }
}
