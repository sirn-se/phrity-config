<?php

namespace Phrity\Config;

class EnvReader implements ReaderInterface
{
    use TreeTrait;

    private string $class;

    public function __construct(string $class = Configuration::class, string|null $separator = null)
    {
        $this->class = $class;
        $this->separator = $separator;
    }

    public function createConfiguration(array|null $match = null): Configuration
    {
        $env = array_change_key_case(getenv());
        if (!is_null($match)) {
            $env = array_intersect_key($env, array_change_key_case(array_flip($match)));
        }
        return new $this->class(is_null($this->separator) ? $env : $this->toTree($env));
    }
}
