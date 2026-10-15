[Documentation](../README.md) / Configuration

# Configuration

The `Phrity\Config\Configuration` class implements the `[ConfigurationInterface](ConfigurationInterface.md)`.

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

Configuration can be accessed by path

```php
$config->has('b/bb'); // => true
$config->get('b/bb'); // => 66
```

### Specifying default

If default is specified, non-exising configuration will return that value instead of throwing exception.

```php
$config->get('a', default: 99); // => 23
$config->get('c', default: 99); // => 99
```

### Type coercion

Configuration types can be coerced into another type.

```php
$config->get('a', coerce: 'string'); // => "23"
```

See [Transformers](https://github.com/sirn-se/phrity-util-transformer) for coerscion options.
Any coercion not specified will cause a `CoercionException`.

### Merging configurations

Configurations can be merged (immutable, new instance will be returned).

```php
$additional = new Configuration(['c' => 12, 'b' => ['bc' => 13]]);
$merged = $config->merge($additional);
```
