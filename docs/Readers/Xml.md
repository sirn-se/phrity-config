[Documentation](../../README.md) / Readers / XML

# XML readers

Readers for XML input.

⚠️ XML readers require `libxml` extension

## The XmlReader

```php
$reader = new XmlReader();

// Read from Neon string
$configuration = $reader->createConfiguration('<a><b>Hello</b></a>');
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
    string $xml = '',
): ConfigurationInterface;
```

* `xml` - XML string to parse.

## The XMLFileReader

```php
$reader = new XmlFileReader();

// Read from file
$configuration = $reader->createConfiguration(path: 'path/to/file.xml');
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
    string $path = 'config.nexmlon',
): ConfigurationInterface;
```

* `path` - File to be read.
