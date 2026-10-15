[Documentation](../README.md) / ConfigurationFactory

# Configuration factory

The `Phrity\Config\ConfigurationFactory` provides shortcuts to create and merge configurations.

```php
$factory = new Phrity\Config\ConfigurationFactory();

$configData = $factory->fromData(data: ['a' => 23]);
$configJson = $factory->fromJson(json: '{"a": 23}');
$configJsonFile = $factory->fromJsonFile(path: 'path/to/config.json');
$configYaml = $factory->fromYaml(yaml: 'n: 23');
$configYamlFile = $factory->fromYamlFile(path: 'path/to/config.yaml');
$configNeon = $factory->fromNeon(neon: 'n: 23');
$configNeonFile = $factory->fromNeonFile(path: 'path/to/config.neon');
$configEnv = $factory->fromEnv();
$configEnvFile = $factory->fromEnvFile('.env');
$configDirectory = $factory->fromDirectory('path/to/config/*json');

$configMerged = $factory->merge(
    $configData,
    $configJson,
    $configJsonFile,
    $configYaml,
    $configYamlFile,
    $configNeon,
    $configNeonFile,
    $configEnv,
    $configEnvFile,
    $configDirectory,
);
```
