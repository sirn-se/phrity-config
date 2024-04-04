<?php

namespace Phrity\Config;

function class_exists(string $class, bool $autoload = true): bool
{
    return isset($GLOBALS['class_exists']) ? $GLOBALS['class_exists'] : \class_exists($class, $autoload);
}
