<?php

namespace Phrity\Config;

class JsonFileWriter implements FileWriterInterface
{
    use FileTrait;
    use JsonTrait;

    private bool $pretty;

    public function __construct(
        string $prefix = '',
        bool $pretty = false,
    ) {
        $this->prefix = $prefix;
        $this->pretty = $pretty;
    }

    public function writeConfiguration(
        ConfigurationInterface $configuration,
        string $path = '',
    ): void {
        $encoded = $this->jsonEncode($configuration, $this->pretty);
        $this->writeFile($encoded, $path);
    }
}
