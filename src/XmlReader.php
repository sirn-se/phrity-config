<?php

namespace Phrity\Config;

/**
 * @template T of ConfigurationInterface
 */
class XmlReader implements ReaderInterface
{
    use XmlTrait;

    /** @var class-string<T> $class */
    protected string $class;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        string $class = Configuration::class,
    ) {
        $this->xmlInstalled();
        $this->class = $class;
    }

    /**
     * @return T
     */
    public function createConfiguration(
        string $xml = '',
    ): ConfigurationInterface {
        return new $this->class($this->xmlResolve($xml));
    }
}
