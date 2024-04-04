<?php

namespace Phrity\Config;

use Phrity\Util\AccessorTrait;

class Configuration implements ConfigurationInterface
{
    use AccessorTrait;

    private object $config;


    /* ---------- Public methods ----------------------------------------------------------------------------------- */

    /**
     * Create new Configuration insatnce.
     * @param object|array $config Source config data
     */
    public function __construct(object|array $config = [])
    {
        $this->config = $this->normalize($config);
    }

    /**
     * Get config value, optionally with default.
     * @param string $id Config identifier, support path resolve i.e. "my/conf/value"
     * @param mixed $opt Options
     *   - mixed $default Return default value if path not set
     * @return mixed Requested config value
     * @throws NotFoundException If requested config not set and no default specified
     */
    public function get(string $id, mixed ...$opt): mixed
    {
        if (!$this->has($id) && !isset($opt['default'])) {
            throw new NotFoundException("No configuration entry with id '{$id}'.");
        }
        $path = $this->accessorParsePath(strtolower($id), '/');
        $data = $this->accessorGet($this->config, $path, $opt['default'] ?? null);
        return is_object($data) ? new self($data) : $data;
    }

    /**
     * If config value is exists.
     * @param string $id Config identifier, support path resolve i.e. "my/conf/value"
     * @return bool True if config exist
     */
    public function has(string $id): bool
    {
        $path = $this->accessorParsePath(strtolower($id), '/');
        return $this->accessorHas($this->config, $path);
    }

    /**
     * Merge this with another Configuration instance and return.
     * @param Configuration $config The Configuration instance to merge
     * @return Configuration New Configuration instance with merged result
     */
    public function merge(Configuration $config): self
    {
        return new self($this->merger($this->config, $config->config));
    }


    /* ---------- Private helper methods --------------------------------------------------------------------------- */

    private function normalize(mixed $data): mixed
    {
        if (is_scalar($data)) {
            return $data;
        }
        if (is_array($data) && array_is_list($data)) {
            return array_map(function (mixed $value): mixed {
                return $this->normalize($value);
            }, $data);
        }
        $changed = (object)[];
        array_walk($data, function (mixed $value, string $key) use ($changed) {
            $key = strtolower($key);
            $changed->$key = $this->normalize($value);
        });
        return (object)$changed;
    }

    private function merger(object $d1, object $d2)
    {
        $changed = clone $d1;
        array_walk($d2, function (mixed $value, string $key) use ($changed) {
            if (!property_exists($changed, $key) || gettype($value) != gettype($changed->$key) || is_scalar($value)) {
                $changed->$key = $value;
            } elseif (is_array($value)) {
                $changed->$key = array_values(array_merge($changed->$key, $value));
            } elseif (is_object($value)) {
                $changed->$key = $this->merger($changed->$key, $value);
            }
        });
        return $changed;
    }
}
