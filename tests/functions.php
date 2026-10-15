<?php

/**
 * This file overloads some core function for testing.
 */

namespace Phrity\Config;

function class_exists(string $class, bool $autoload = true): bool
{
    return isset($GLOBALS['class_exists'])
        ? $GLOBALS['class_exists']
        : \class_exists($class, $autoload);
}

function is_readable(string $filename): bool
{
    return isset($GLOBALS['is_readable'])
        ? $GLOBALS['is_readable']
        : \is_readable($filename);
}

function is_dir(string $filename): bool
{
    return isset($GLOBALS['is_dir'])
        ? $GLOBALS['is_dir']
        : \is_dir($filename);
}

function mkdir(string $directory, int $permissions = 0777, bool $recursive = false): bool
{
    return isset($GLOBALS['mkdir'])
        ? $GLOBALS['mkdir']
        : \mkdir($directory, $permissions, $recursive);
}

function file_put_contents(string $filename, mixed $data, int $flags = 0): bool
{
    return isset($GLOBALS['file_put_contents'])
        ? $GLOBALS['file_put_contents']
        : \file_put_contents($filename, $data, $flags);
}
