<?php

namespace Phrity\Config;

use Phrity\Util\AccessorTrait;

class Configuration implements ConfigurationInterface
{
    use AccessorTrait;

    protected object $config;


    /* ---------- Public methods ----------------------------------------------------------------------------------- */

    /**
     * Create new Configuration insatnce.
     * @param object|array<mixed> $config Source config data
     */
    public function __construct(object|array $config = [])
    {
        $this->config = (object)$this->normalize($config);
    }

    /**
     * Get config value, optionally with default.
     * @param string $id Config identifier, support path resolve i.e. "my/conf/value"
     * @param mixed ...$opt Options
     *   - default Return default value if path not set
     *   - coerce Cast type
     * @return mixed Requested config value
     * @throws NotFoundException If requested config not set and no default specified
     * @throws CoercionException If config could not be coerced
     */
    public function get(string $id, mixed ...$opt): mixed
    {
        if (!$this->has($id) && !isset($opt['default'])) {
            throw new NotFoundException("No configuration entry with id '{$id}'.");
        }
        $opt = array_merge(['default' => null, 'coerce' => null], $opt);
        $path = $this->accessorParsePath(strtolower($id), '/');
        $data = $this->accessorGet($this->config, $path, $opt['default']);
        return $opt['coerce'] ? $this->coerce((string)$opt['coerce'], $data) : $data;
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
     * @param ConfigurationInterface $config The Configuration instance to merge
     * @return Configuration New Configuration instance with merged result
     */
    public function merge(ConfigurationInterface $config): self
    {
        return new self($this->merger($this->config, $config->jsonSerialize()));
    }

    /**
     * Return as anonymous onject.
     * @return object
     */
    public function jsonSerialize(): object
    {
        return $this->config;
    }


    /* ---------- Private helper methods --------------------------------------------------------------------------- */

    protected function coerce(string $type, mixed $data): mixed
    {
        $dataType = strtolower(gettype($data));
        if ($dataType == $type) {
            return $data;
        }
        switch ($type) {
            case 'boolean':
                switch ($dataType) {
                    case 'double':
                    case 'integer':
                        if ($data === 0 || $data === 0.0) {
                            return false;
                        }
                        if ($data === 1 || $data === 1.0) {
                            return true;
                        }
                        throw new CoercionException("Failed to coerce {$dataType} {$data} to boolean");
                    case 'string':
                        if (in_array(strtolower($data), ['', '0', 'false'])) {
                            return false;
                        }
                        if (in_array(strtolower($data), ['1', 'true'])) {
                            return true;
                        }
                        throw new CoercionException("Failed to coerce {$dataType} '{$data}' to boolean");
                    case 'null':
                        return false;
                    default:
                        throw new CoercionException("Failed to coerce {$dataType} to boolean");
                }
            case 'integer':
                switch ($dataType) {
                    case 'string':
                        if (is_numeric($data)) {
                            return (int)$data;
                        }
                        throw new CoercionException("Failed to coerce {$dataType} '{$data}' to integer");
                    case 'null':
                        return 0;
                    case 'double':
                        return (int)$data;
                    case 'boolean':
                        return $data ? 1 : 0;
                    default:
                        throw new CoercionException("Failed to coerce {$dataType} to integer");
                }
            case 'double':
                switch ($dataType) {
                    case 'string':
                        if (is_numeric($data)) {
                            return (double)$data;
                        }
                        throw new CoercionException("Failed to coerce {$dataType} '{$data}' to double");
                    case 'null':
                        return 0.0;
                    case 'integer':
                        return (double)$data;
                    case 'boolean':
                        return $data ? 1.0 : 0.0;
                    default:
                        throw new CoercionException("Failed to coerce {$dataType} to double");
                }
            case 'string':
                switch ($dataType) {
                    case 'double':
                    case 'integer':
                        return (string)$data;
                    case 'null':
                        return 'null';
                    case 'boolean':
                        return $data ? 'true' : 'false';
                    default:
                        throw new CoercionException("Failed to coerce {$dataType} to string");
                }
            case 'null':
                switch ($dataType) {
                    case 'double':
                    case 'integer':
                        if ((double)$data === 0.0) {
                            return null;
                        }
                        throw new CoercionException("Failed to coerce {$dataType} {$data} to null");
                    case 'boolean':
                        if ($data === false) {
                            return null;
                        }
                        throw new CoercionException("Failed to coerce {$dataType} {$data} to null");
                    case 'string':
                        if (in_array(strtolower($data), ['', '0', 'null'])) {
                            return null;
                        }
                        throw new CoercionException("Failed to coerce {$dataType} '{$data}' to null");
                    default:
                        throw new CoercionException("Failed to coerce {$dataType} to null");
                }
        }
        throw new CoercionException("Invalid coercion type '{$type}'");
    }

    protected function normalize(mixed $data): mixed
    {
        if (is_scalar($data) || is_null($data)) {
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

    protected function merger(object $d1, object $d2): object
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
