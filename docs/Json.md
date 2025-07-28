[Documentation](../README.md) / JSON

# JSON readers

Readers for JSON input.

## The JsonReader

```php
$reader = new JsonReader();

// Read from JSON string
$configuration = $reader->createConfiguration('{"a": 1}');
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
    string $json = '{}',
): ConfigurationInterface;
```

* `json` - JSON string to parse.

## The JsonFileReader

```php
$reader = new JsonFileReader();

// Read from file
$configuration = $reader->createConfiguration(path: 'path/to/file.json');
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
    string $path = 'config.json',
): ConfigurationInterface;
```

* `path` - File to be read.
