[Documentation](../README.md) / Data

# Data readers

Reader for PHP data input.

## The DataReader

```php
$reader = new DataReader();

// Associative array as source
$configuration = $reader->createConfiguration(['a' => 1]);
// Object as source
$configuration = $reader->createConfiguration((object)['a' => 1]);
// Null as source
$configuration = $reader->createConfiguration(null);
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
    object|array|null $data = null,
): ConfigurationInterface;
```

* `data` - Input to be read as object, associative array or null.
