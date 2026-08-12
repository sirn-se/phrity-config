<?php

namespace Phrity\Config;

/**
 * @template T of ConfigurationInterface
 */
class NeonReader implements ReaderInterface
{
    use NeonTrait;

    /** @var class-string<T> $class */
    protected string $class;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        string $class = Configuration::class,
    ) {
        $this->neonInstalled();
        $this->class = $class;
    }

    /**
     * @return T
     */
    public function createConfiguration(
        string $neon = '{}',
    ): ConfigurationInterface {
        return new $this->class($this->neonDecode($neon));
    }
}
