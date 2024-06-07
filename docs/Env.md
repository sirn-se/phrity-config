[Documentation](../README.md) / ENV

# Env readers

Readers for ENV input.

## The EnvReader

```php
$reader = new EnvReader();

// Read from environment variables
$configuration = $reader->createConfiguration();
```

### Constructor options

```php
public function __construct(
    string $class = Configuration::class,
    string|null $separator = null,
);
```

* `class` - An instance of any class implementing `ConfigurationInterface` to be returned (default `Configuration`).
* `separator` - Will parse input into tree structure using separator.

### createConfiguration options

```php
public function createConfiguration(
    array|null $match = null,
): ConfigurationInterface;
```

* `match` - If specified, only matching environment variables will be read.

## The EnvFileReader

```php
$reader = new EnvFileReader();

// Read from file
$configuration = $reader->createConfiguration(path: 'path/to/file.env');
```

### Constructor options

```php
public function __construct(
    string $class = Configuration::class,
    string $prefix = '',
    string|null $separator = null,
    bool $optional = false,
);
```

* `class` - An instance of any class implementing `ConfigurationInterface` to be returned (default `Configuration`).
* `prefix` - File path prefix.
* `separator` - Will parse input into tree structure using separator.
* `optional` - If true, reader will return empty configuration if file is missing.

### createConfiguration options

```php
public function createConfiguration(
    string $path = '.env',
    array|null $match = null,
): ConfigurationInterface;
```

* `path` - File to be read.
* `match` - If specified, only matching environment variables will be read.
