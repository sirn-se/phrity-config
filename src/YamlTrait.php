<?php

namespace Phrity\Config;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

trait YamlTrait
{
    protected Parser|null $parser = null;

    public function yamlInstalled(): void
    {
        if (!class_exists(Parser::class)) {
            throw new ReaderException("Dependency 'symfony/yaml' not installed, can not read YAML file.");
        }
    }

    public function yamlDecode(string $yaml): mixed
    {
        if ($this->parser === null) {
            $this->parser = new Parser();
        }
        try {
            return $this->parser->parse($yaml);
        } catch (ParseException $e) {
            throw new ReaderException("YAML: {$e->getMessage()}", 0, $e);
        }
    }
}
