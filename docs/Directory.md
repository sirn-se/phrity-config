[Documentation](../README.md) / Directory

# Directory reader

Reads and merges all supported configuration files in directory, optionally with filter.

## The DirectoryReader

```php
$reader = new DirectoryReader();

// Read all files in directory
$configuration = $reader->createConfiguration(path: 'my/config/directory/');
```

### Constructor options

```php
public function __construct(
    string $class = Configuration::class,
    array|null $readers = null,
);
```

* `class` - An instance of any class implementing `ConfigurationInterface` to be returned (default `Configuration`).
* `readers` - Optional associative array of file readers. File extension as key, file reader class as class-string or instance.

Default readers are;
```php
[
    'json' => JsonFileReader::class,
    'neon' => NeonFileReader::class,
    'yaml' => YamlFileReader::class,
],
```

### createConfiguration options

```php
public function createConfiguration(
    string $path = 'config/',
): ConfigurationInterface;
```

* `path` - Directory to be read.

The `path` parameter may take a pattern compatible for [glob](https://www.php.net/manual/en/function.glob.php) (including brace option).

Examples;
- `my/config/directory` - All files in directory
- `my/config/directory/config.json` - Specific file
- `my/config/directory/*.json` - All files with extension
- `my/config/directory/{config1.json,config2.yaml}` - Multiple files
- `my/config/directory/*.{json,yaml}` - All files with extensions
- `my/config/*/*.json` - All files with extension in subdirectories
