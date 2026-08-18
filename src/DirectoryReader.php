<?php

namespace Phrity\Config;

/**
 * @template T of ConfigurationInterface
 */
class DirectoryReader implements ReaderInterface
{
    use FileTrait;

    /** @var class-string<T> $class */
    protected string $class;
    /** @var array<string, class-string<FileReaderInterface>|FileReaderInterface> $readers */
    private array $readers = [];

    /**
     * @param class-string<T> $class
     * @param array<string, class-string<FileReaderInterface>|FileReaderInterface>|null $readers
     */
    public function __construct(
        string $class = Configuration::class,
        array|null $readers = null,
    ) {
        $this->class = $class;
        $this->readers = $readers ?? [
            'json' => JsonFileReader::class,
            'neon' => NeonFileReader::class,
            'yaml' => YamlFileReader::class,
        ];
    }

    /**
     * @param string $path
     * @return ConfigurationInterface
     */
    public function createConfiguration(
        string $path = 'config/',
    ): ConfigurationInterface {
        $configuration = new $this->class();
        $files = glob($path, GLOB_BRACE | GLOB_ERR);
        if ($files === false || $files === []) {
            throw new ReaderException("Could not read pattern '{$path}'.");
        }
        foreach ($files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            // Ignore undefined readers
            if (!array_key_exists($ext, $this->readers)) {
                continue;
            }
            // JIT-load reader
            if (is_string($this->readers[$ext])) {
                $this->readers[$ext] = new $this->readers[$ext]($this->class);
            }
            $configuration = $configuration->merge($this->readers[$ext]->createConfiguration($file));
        }
        return $configuration;
    }
}
