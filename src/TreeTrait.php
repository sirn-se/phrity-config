<?php

namespace Phrity\Config;

trait TreeTrait
{
    private string|null $separator = null;

    /**
     * @param array<mixed> $data
     */
    private function toTree(array $data): mixed
    {
        if (empty($this->separator)) {
            return $data; // No action
        }
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
            $coll[$key] = $this->toTree($sub);
        }
        if (count($coll) === 1 && isset($coll[$this->separator])) {
            return $coll[$this->separator];
        }
        return $coll;
    }
}
