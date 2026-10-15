[Documentation](../../README.md) / Writers / JSON

# JSON writer

Writer for JSON output.

## The JsonFileWriter

```php
$writer = new JsonFileWriter();

// Write to file
$writer->writeConfiguration($configuration, 'path/to/file.json');
```

### Constructor options

```php
public function __construct(
    string $prefix = '',
    bool $pretty = false,
);
```

* `prefix` - File path prefix.
* `pretty` - If true, will write more readable (as with `JSON_PRETTY_PRINT` flag).

### writeConfiguration options

```php
public function writeConfiguration(
    ConfigurationInterface $configuration,
    string $path = '',
): void;
```

* `ConfigurationInterface` - Configuration to write
* `path` - File to be read.
