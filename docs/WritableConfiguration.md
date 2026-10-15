[Documentation](../README.md) / WritableConfiguration

# Writable Configuration

The `Phrity\Config\WritableConfiguration` class implements the `[ConfigurationInterface](ConfigurationInterface.md)`.
Identical to Configuration but also support writing values on the configuration.

```php
use Phrity\Config\WritableConfiguration;

// Initiate with object or associative array
$config = new WritableConfiguration([
    'a' => 23,
    'b' => [
        'bb' => 66,
    ],
]);

// Setting values on current configuration
$config->set('a', 'New value');

// Immutable; set value on new copy, leaving original as is
$copy = $config->with('a', 'New value');
```

