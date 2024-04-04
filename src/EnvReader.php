<?php

namespace Phrity\Config;

class EnvReader implements ReaderInterface
{
    private string $class;
    private string|null $separator;

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
        return new $this->class(is_null($this->separator) ? $env : $this->split($env));
    }

    private function split(array $data): mixed
    {
        $re = "|^([{$this->separator}]*[^{$this->separator}]+){$this->separator}(.+)|";
        $coll = [];
        foreach ($data as $key => $value) {
            if ($key == $this->separator) {
                $coll[$this->separator] = $value;
                continue;
            }
            preg_match($re, $key, $res);
            $keyf = $res[1] ?? $key;
            $keyl = $res[2] ?? $this->separator;
            $coll[$keyf][$keyl] = $value;
        }
        foreach ($coll as $key => $sub) {
            if (!is_array($sub)) {
                continue;
            }
            $coll[$key] = $this->split($sub);
        }
        if (count($coll) === 1 && isset($coll[$this->separator])) {
            return $coll[$this->separator];
        }
        return $coll;
    }
}
