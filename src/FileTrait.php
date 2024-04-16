<?php

namespace Phrity\Config;

trait FileTrait
{
    protected string $prefix = '';
    protected bool $optional = false;

    public function readFile(string $path): string|null
    {
        $file = "{$this->prefix}{$path}";
        if (!is_file($file)) {
            if ($this->optional) {
                return null;
            }
            throw new ReaderException("File '{$file}' not found.");
        }
        if (!is_readable($file)) {
            throw new ReaderException("File '{$file}' can not be read.");
        }
        return file_get_contents($file);
    }
}
