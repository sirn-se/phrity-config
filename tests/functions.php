<?php

namespace Phrity\Config;

function class_exists(string $class, bool $autoload = true): bool
{
    return isset($GLOBALS['class_exists']) ? $GLOBALS['class_exists'] : \class_exists($class, $autoload);
}

function is_readable(string $filename): bool
{
    return isset($GLOBALS['is_readable']) ? $GLOBALS['is_readable'] : \is_readable($filename);
}