<?php

namespace Phrity\Config;

use JsonException;

/**
 * @template T of ConfigurationInterface
 */
class JsonReader implements ReaderInterface
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
     * @return T
     */
    public function createConfiguration(
        string $json = '{}',
    ): ConfigurationInterface {
        try {
            $data = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
            return new $this->class($data);
        } catch (JsonException $e) {
            throw new ReaderException("JSON: {$e->getMessage()}");
        }
    }
}
