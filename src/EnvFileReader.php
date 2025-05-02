<?php

namespace Phrity\Config;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\FormatException;

/**
 * @template T of ConfigurationInterface
 */
class EnvFileReader implements ReaderInterface
{
    use FileTrait;
    use TreeTrait;

    /** @var class-string<T> $class */
    protected string $class;
    protected Dotenv $parser;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        string $class = Configuration::class,
        string $prefix = '',
        string|null $separator = null,
        bool $optional = false,
    ) {
        if (!class_exists(Dotenv::class)) {
            throw new ReaderException("Dependency 'symfony/dotenv' not installed, can not read .env file.");
        }
        $this->class = $class;
        $this->separator = $separator;
        $this->prefix = $prefix;
        $this->optional = $optional;
        $this->parser = new Dotenv();
    }

    /**
     * @param string $path
     * @param array<mixed>|null $match
     * @return T
     */
    public function createConfiguration(
        string $path = '.env',
        array|null $match = null,
    ): ConfigurationInterface {
        $content = $this->readFile($path);
        if (is_null($content)) {
            return new $this->class();
        }
        try {
            $env = array_change_key_case($this->parser->parse($content));
            if (!is_null($match)) {
                $env = array_intersect_key($env, array_change_key_case(array_flip($match)));
            }
            return new $this->class($this->toTree($env));
        } catch (FormatException $e) {
            throw new ReaderException("ENV: {$e->getMessage()}");
        }
    }
}
