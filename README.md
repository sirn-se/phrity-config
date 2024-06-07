[![Build Status](https://github.com/sirn-se/phrity-config/actions/workflows/acceptance.yml/badge.svg)](https://github.com/sirn-se/phrity-config/actions)
[![Coverage Status](https://coveralls.io/repos/github/sirn-se/phrity-config/badge.svg?branch=main)](https://coveralls.io/github/sirn-se/phrity-config?branch=main)

# Introduction

Tools for handling configuration.
Interfaces, implementation class, various readers and a factory.

## Installation

Install with [Composer](https://getcomposer.org/);
```
composer require phrity/config
```

## The `ConfigurationInterface` interface

The `Phrity\Config\ConfigurationInterface` extends
[PSR-11 ContainerInterface](https://www.php-fig.org/psr/psr-11/) and
[JsonSerializable](https://www.php.net/manual/en/class.jsonserializable) interfaces.

```php
// ContainerInterface implementation
public function get(string $id): mixed;
public function has(string $id): bool;

// JsonSerializable implementation
public function jsonSerialize(): mixed;

// Additional methods
public function __construct(object|array $config);
public function merge(ConfigurationInterface $config): ConfigurationInterface;
```

## The `Configuration` class

The `Phrity\Config\Configuration` class implements the `ConfigurationInterface`.

```php
use Phrity\Config\Configuration;

// Initiate with object or associative array
$config = new Configuration([
    'a' => 23,
    'b' => [
        'bb' => 66,
    ],
]);

// Check and get (case insensitive) from configuration
$config->has('a'); // => true
$config->get('a'); // => 23

// Trying to get non-exising configuration will throw exception
$config->has('c'); // => false
$config->get('c'); // throws NotFoundException
```

### Accessing by path

```php
// It is possible to access by path
$config->has('b/bb'); // => true
$config->get('b/bb'); // => 66
```

### Specifying default

```php
// If default is specified, non-exising configuration will return that value instead of throwing exception
$config->get('a', default: 99); // => 23
$config->get('c', default: 99); // => 99
```

### Type coercion

```php
// Some types can be coerced into another type
$config->get('a', coerce: 'string'); // => "23"
```

* Target type `boolean`
  * 0, 0.0, "0", "0.0", "", "false" and null will be coerced to false
  * 1, 1.1, "1",  "1.0" and "true" will be coerced to true
* Target types `integer` and `double`
  * numeric string will be coerced to integer or double
  * null and false will be coerced to 0 or 0.0
  * true will be coerced to 1 or 1.0
* Target type `string`
  * integer and double will be coerced to string
  * null will be coerced to "null"
  * false will be coerced to "false"
  * true will be coerced to "true"
* Target type `null`
  * 0, 0.0, "0", "0.0", "" and "null" will be coerced to null

Any coercion not specified above will cause a `CoercionException`.

### Merging configurations

```php
// Configurations can be merged (immutable, new instance will be returned)
$additional = new Configuration(['c' => 12, 'b' => ['bc' => 13]]);
$merged = $config->merge($additional);
```

## The Reader classes

A number of configuration readers are available.

* [DataReader](docs/Data.md) - Reader for PHP data input
* [EnvReader and EnvFileReader](docs/Env.md) - Readers for ENV input
* [JsonReader and JsonFileReader](docs/Json.md) - Readers for JSON input
* [YamlReader and YamlFileReader](docs/Yaml.md) - Readers for YAML input

## The `ConfigurationFactory` class

The `Phrity\Config\ConfigurationFactory` provides shortcuts to create and merge configurations.

```php
$factory = new Phrity\Config\ConfigurationFactory();

$configData = $factory->fromData(data: ['a' => 23]);
$configJson = $factory->fromJson(json: '{"a": 23}');
$configJsonFile = $factory->fromJsonFile(path: 'path/to/config.json');
$configYaml = $factory->fromYaml(yaml: 'n: 23');
$configYamlFile = $factory->fromYamlFile(path: 'path/to/config.yaml');
$configEnv = $factory->fromEnv();
$configEnvFile = $factory->fromEnvFile('.env');

$configMerged = $factory->merge(
    $configData,
    $configJson,
    $configJsonFile,
    $configYaml,
    $configYamlFile,
    $configEnv,
    $configEnvlFile,
);
```


## Versions

| Version | PHP | |
| --- | --- | --- |
| `1.3` | `^8.1` | Coerce option |
| `1.2` | `^8.1` | Reader (data), all file-readers get `optional` option |
| `1.1` | `^8.1` | Readers (yaml, env-file) |
| `1.0` | `^8.1` | Interface, implementation, readers (json, json-file, yaml-file, env), factory |
