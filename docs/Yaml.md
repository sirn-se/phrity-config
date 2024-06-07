[Documentation](../README.md) / YAML

# Env readers

Readers for YAML input.

## The YamlReader

```php
$reader = new YamlReader();

// Read from YAML string
$configuration = $reader->createConfiguration('a: 1');
```

### Constructor options

```php
public function __construct(
    string $class = Configuration::class,
);
```

* `class` - An instance of any class implementing `ConfigurationInterface` to be returned (default `Configuration`).

### createConfiguration options

```php
public function createConfiguration(
    string $yaml = '{}',
): ConfigurationInterface;
```

* `yaml` - YAML string to parse.

## The YamlFileReader

```php
$reader = new YamlFileReader();

// Read from file
$configuration = $reader->createConfiguration(path: 'path/to/file.yaml');
```

### Constructor options

```php
public function __construct(
    string $class = Configuration::class,
    string $prefix = '',
    bool $optional = false,
);
```

* `class` - An instance of any class implementing `ConfigurationInterface` to be returned (default `Configuration`).
* `prefix` - File path prefix.
* `optional` - If true, reader will return empty configuration if file is missing.

### createConfiguration options

```php
public function createConfiguration(
    string $path = 'config.yaml',
): ConfigurationInterface;
```

* `path` - File to be read.
