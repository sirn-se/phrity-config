<?php

namespace Phrity\Config;

/**
 * @template T of ConfigurationInterface
 */
class XmlFileReader implements FileReaderInterface
{
    use FileTrait;
    use XmlTrait;

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
        $this->xmlInstalled();
        $this->class = $class;
        $this->prefix = $prefix;
        $this->optional = $optional;
    }

    /**
     * @return T
     */
    public function createConfiguration(
        string $path = 'config.xml'
    ): ConfigurationInterface {
        $xml = $this->readFile($path);
        if (is_null($xml)) {
            return new $this->class();
        }
        return new $this->class($this->xmlResolve($xml));
    }
}
