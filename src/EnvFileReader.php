<?php

namespace Phrity\Config;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\FormatException;

class EnvFileReader implements ReaderInterface
{
    use FileTrait;
    use TreeTrait;

    private string $class;
    private Dotenv $parser;

    public function __construct(
        string $class = Configuration::class,
        string $prefix = '',
        string|null $separator = null
    ) {
        if (!class_exists(Dotenv::class)) {
            throw new ReaderException("Dependency 'symfony/dotenv' not installed, can not read .env file.");
        }
        $this->class = $class;
        $this->separator = $separator;
        $this->prefix = $prefix;
        $this->parser = new Dotenv();
    }

    public function createConfiguration(string $path = '.env', array|null $match = null): ConfigurationInterface
    {
        $content = $this->readFile($path);
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
