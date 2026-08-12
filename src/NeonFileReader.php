<?php

namespace Phrity\Config;

/**
 * @template T of ConfigurationInterface
 */
class NeonFileReader implements FileReaderInterface
{
    use FileTrait;
    use NeonTrait;

    /** @var class-string<T> $class */
    protected string $class;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        string $class = Configuration::class,
        string $prefix = '',
        bool $optional = false,
    ) {
        $this->neonInstalled();
        $this->class = $class;
        $this->prefix = $prefix;
        $this->optional = $optional;
    }

    /**
     * @return T
     */
    public function createConfiguration(
        string $path = 'config.neon'
    ): ConfigurationInterface {
        $neon = $this->readFile($path);
        if (is_null($neon)) {
            return new $this->class();
        }
        return new $this->class($this->neonDecode($neon));
    }
}
