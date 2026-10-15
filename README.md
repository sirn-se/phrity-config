<p align="center"><img src="docs/logotype.png" alt="Phrity Config" width="100%"></p>

[![Build Status](https://github.com/sirn-se/phrity-config/actions/workflows/acceptance.yml/badge.svg)](https://github.com/sirn-se/phrity-config/actions)

# Introduction

Tools for handling configuration.
Interfaces, implementation classes, various readers, writers and a factory.

## Installation

Install with [Composer](https://getcomposer.org/);
```
composer require phrity/config
```

# Documentation

## Configuration

Implements [PSR-11 ContainerInterface](https://www.php-fig.org/psr/psr-11/), providing `get()` and `has()` methods.

```php
$config = new Configuration(['opt1' => 11, 'opt2' => 22]);
$config->has('opt1'); // -> true
$config->get('opt1'); // -> 11
```

Support path access, default value and type coercion.
```php
$config->has('a/b/c');
$config->get('a/b/c', default: 99, coerce: 'string');
```

Support merging.
```php
$merged = $config->merge(new Configuration($data));
```

Read more on [Configuration](docs/Configuration.md).

## Writable configuration

Identical to Configuration but also support writing values on the configuration.
```php
$config = new WritableConfiguration(['opt1' => 11, 'opt2' => 22]);
$config->set('opt1', 123);
$config->with('opt1', 123);
```

Read more on [WritableConfiguration](docs/WritableConfiguration.md).

## Reader classes

Classes that create configuration instance from various sources.

```php
$reader = new JsonFileReader();
$config = $reader->createConfiguration(path: 'path/to/file.json');
```

List of available readers:

* [DataReader](docs/Readers/Data.md) - Reader for PHP data input
* [DirectoryReader](docs/Readers/Directory.md) - Reads and merges all supported configuration files in directory
* [EnvReader and EnvFileReader](docs/Readers/Env.md) - Readers for ENV input (file reader requires `symfony/dotenv`)
* [JsonReader and JsonFileReader](docs/Readers/Json.md) - Readers for JSON input
* [NeonReader and NeonFileReader](docs/Readers/Neon.md) - Reader for NEON input (requires `nette/neon`)
* [YamlReader and YamlFileReader](docs/Readers/Yaml.md) - Readers for YAML input (requires `symfony/yaml`)

## Writer classes

Classes that write configuration to file.

```php
$reader = new JsonFileWriter();
$config = $reader->writeConfiguration($config, 'path/to/file.json');
```

List of available writers:

* [JsonFileWriter](docs/Readers/Json.md) - Writer for JSON output

##  Factory

A factory that provides shortcuts to create and merge configurations.

```php
$factory = new ConfigurationFactory();
$config = $factory->fromJsonFile(path: 'path/to/config.json');
```

Read more on [ConfigurationFactory](docs/ConfigurationFactory.md).

