<?php

namespace Phrity\Config;

/**
 * @template T of ConfigurationInterface
 */
class DataReader implements ReaderInterface
{
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
     * @param object|array<mixed>|null $data
     * @return T
     */
    public function createConfiguration(
        object|array|null $data = null,
    ): ConfigurationInterface {
        return new $this->class($data ?? []);
    }
}
