<?php

namespace Phrity\Config;

use JsonException;

class JsonReader implements ReaderInterface
{
    protected string $class;

    public function __construct(
        string $class = Configuration::class,
    ) {
        $this->class = $class;
    }

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
