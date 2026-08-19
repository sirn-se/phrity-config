<?php

namespace Phrity\Config;

use JsonException;

trait JsonTrait
{
    public function jsonDecode(string $json): mixed
    {
        try {
            return json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ReaderException("JSON: {$e->getMessage()}", 0, $e);
        }
    }
}
