<?php

namespace Phrity\Config;

use JsonException;

trait JsonTrait
{
    public function jsonDecode(string $json): mixed
    {
        try {
            return json_decode($json, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ReaderException("JSON: {$e->getMessage()}", 0, $e);
        }
    }

    public function jsonEncode(mixed $data, bool $pretty = false): string
    {
        $flags = $pretty ? JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR : JSON_THROW_ON_ERROR;
        return json_encode($data, flags: $flags);
    }
}
