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
        return file_get_contents($file) ?: null;
    }

    public function writeFile(mixed $data, string $path): void
    {
        $file = "{$this->prefix}{$path}";
        $dir = dirname($file);
        if (!is_dir($dir)) {
            $result = mkdir($dir, recursive: true);
            if ($result === false) {
                throw new WriterException("Directory '{$dir}' can not be written.");
            }
        }
        $result = file_put_contents($file, $data);
        if ($result === false) {
            throw new WriterException("File '{$file}' can not be written.");
        }
    }
}
