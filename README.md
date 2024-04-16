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
$config = new Configuration(['a' => 23, 'b' => ['bb' => 66]]);

// Check and get (case insensitive) from configuration
$config->has('a'); // => true
$config->get('a'); // => 23

// Trying to get non-exising configuration will throw exception
$config->has('c'); // => false
$config->get('c'); // throws NotFoundException

// It is possible to access by path
$config->has('b/bb'); // => true
$config->get('b/bb'); // => 66

// If default is specified, non-exising configuration will return that value instead of throwing exception
$config->get('a', default: 99); // => 23
$config->get('c', default: 99); // => 99

// Configurations can be merged (immutable, new instance will be returned)
$additional = new Configuration(['c' => 12, 'b' => ['bc' => 13]]);
$merged = $config->merge($additional);
```

## The Reader classes

A number of configuration readers are available.

### The `DataReader` class

The `Phrity\Config\DataReader` takes input as associative array, object, or null.

```php
$reader = new Phrity\Config\DataReader();
$config = $reader->createConfiguration(data: ['a' => 23]);
```

Constructor options
```php
string $class = Configuration::class // Implementation class to create
```
Create options
```php
object|array|null $data // Input
```

### The `JsonReader` class

The `Phrity\Config\JsonReader` parses provided JSON string.

```php
$reader = new Phrity\Config\JsonReader();
$config = $reader->createConfiguration(json: '{"a": 23}');
```

Constructor options
```php
string $class = Configuration::class // Implementation class to create
```
Create options
```php
string $json // JSON-string
```

### The `JsonFileReader` class

The `Phrity\Config\JsonFileReader` parses a file containing JSON.

```php
$reader = new Phrity\Config\JsonFileReader();
$config = $reader->createConfiguration(path: 'path/to/config.json');
```

Constructor options
```php
string $class = Configuration::class // Implementation class to create
string $prefix = '' // Path prefix for files to load
bool $optional = false // Return empty if file do not exist
```
Create options
```php
string $path = 'config.json' // Path to JSON file
```

### The `YamlReader` class

The `Phrity\Config\YamlReader` parses provided YAML string.
The `symfony/yaml` library must be required to use this reader.

```php
$reader = new Phrity\Config\YamlReader();
$config = $reader->createConfiguration(yaml: 'a: 23');
```

Constructor options
```php
string $class = Configuration::class // Implementation class to create
```
Create options
```php
string $yaml // YAML-string
```

### The `YamlFileReader` class

The `Phrity\Config\YamlFileReader` parses a file containing YAML.
The `symfony/yaml` library must be required to use this reader.

```php
$reader = new Phrity\Config\YamlFileReader();
$config = $reader->createConfiguration(path: 'path/to/config.yaml');
```

Constructor options
```php
string $class = Configuration::class // Implementation class to create
string $prefix = '' // Path prefix for files to load
bool $optional = false // Return empty if file do not exist
```
Create options
```php
string $path = 'config.yaml' // Path to YAML file
```

### The `EnvReader` class

The `Phrity\Config\EnvReader` parses environment variables.

```php
$reader = new Phrity\Config\EnvReader();
$config = $reader->createConfiguration();
```

Constructor options
```php
string $class = Configuration::class // Implementation class to create
string|null $separator = null // Separator for converting flat name into hierarchy
```
Create options
```php
array|null $match = null // List of entries to import (all imported if null)
```

### The `EnvFileReader` class

The `Phrity\Config\EnvFileReader` parses a file containing env data.
The `symfony/dotenv` library must be required to use this reader.

```php
$reader = new Phrity\Config\EnvFileReader();
$config = $reader->createConfiguration(path: 'path/to/.env');
```

Constructor options
```php
string $class = Configuration::class // Implementation class to create
string $prefix = '' // Path prefix for files to load
string|null $separator = null // Separator for converting flat name into hierarchy
bool $optional = false // Return empty if file do not exist
```
Create options
```php
string $path = '.env' // Path to .env file
array|null $match = null // List of entries to import (all imported if null)
```

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
| `1.2` | `^8.1` | Reader (data), all file readers get `optional` option |
| `1.1` | `^8.1` | Readers (yaml, env-file) |
| `1.0` | `^8.1` | Interface, implementation, readers (json, json-file, yaml-file, env), factory |
