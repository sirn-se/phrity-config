<?php

namespace Phrity\Config;

trait FileTrait
{
    private string $prefix = '';

    public function readFile(string $path): string
    {
        $file = "{$this->prefix}{$path}";
        if (!is_file($file)) {
            throw new ReaderException("File '{$file}' not found.");
        }
        if (!is_readable($file)) {
            throw new ReaderException("File '{$file}' can not be read.");
        }
        return file_get_contents($file);
    }
}
