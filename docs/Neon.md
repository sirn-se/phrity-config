[Documentation](../README.md) / NEON

# NEON readers

Readers for NEON input.

⚠️ NEON readers require [`nette/neon`](https://packagist.org/packages/nette/neon) to be installed

## The NeonReader

```php
$reader = new NeonReader();

// Read from Neon string
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
    string $neon = '{}',
): ConfigurationInterface;
```

* `neon` - NEON string to parse.

## The NeonFileReader

```php
$reader = new NeonFileReader();

// Read from file
$configuration = $reader->createConfiguration(path: 'path/to/file.neon');
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
    string $path = 'config.neon',
): ConfigurationInterface;
```

* `path` - File to be read.
