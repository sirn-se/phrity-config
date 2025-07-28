<?php

namespace Phrity\Config;

use Nette\Neon\{
    Exception,
    Neon,
};

/**
 * @template T of ConfigurationInterface
 */
class NeonReader implements ReaderInterface
{
    /** @var class-string<T> $class */
    protected string $class;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        string $class = Configuration::class,
    ) {
        if (!class_exists(Neon::class)) {
            throw new ReaderException("Dependency 'nette/neon' not installed, can not read NEON file.");
        }
        $this->class = $class;
    }

    /**
     * @return T
     */
    public function createConfiguration(
        string $neon = '{}',
    ): ConfigurationInterface {
        try {
            $data = Neon::decode($neon);
            if (!is_array($data)) {
                throw new ReaderException("NEON: Invalid input");
            }
            return new $this->class($data);
        } catch (Exception $e) {
            throw new ReaderException("NEON: {$e->getMessage()}");
        }
    }
}
