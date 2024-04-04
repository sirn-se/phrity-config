<?php

namespace Phrity\Config;

class EnvReader implements ReaderInterface
{
    private string $class;
    private string|null $divider;

    public function __construct(string $class = Configuration::class, string|null $divider = null)
    {
        $this->class = $class;
        $this->divider = $divider;
    }

    public function createConfiguration(array|null $match = null): Configuration
    {
        $env = array_change_key_case(getenv());
        if (!is_null($match)) {
            $env = array_intersect_key($env, array_change_key_case(array_flip($match)));
        }
        return new $this->class(is_null($this->divider) ? $env : $this->split($env));
    }

    private function split(array $data): mixed
    {
        $re = "|^([{$this->divider}]*[^{$this->divider}]+){$this->divider}(.+)|";
        $coll = [];
        foreach ($data as $key => $value) {
            if ($key == $this->divider) {
                $coll[$this->divider] = $value;
                continue;
            }
            preg_match($re, $key, $res);
            $keyf = $res[1] ?? $key;
            $keyl = $res[2] ?? $this->divider;
            $coll[$keyf][$keyl] = $value;
        }
        foreach ($coll as $key => $sub) {
            if (!is_array($sub)) {
                continue;
            }
            $coll[$key] = $this->split($sub);
        }
        if (count($coll) === 1 && isset($coll[$this->divider])) {
            return $coll[$this->divider];
        }
        return $coll;
    }
}
