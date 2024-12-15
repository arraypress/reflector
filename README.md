# ArrayPress Reflector

A powerful PHP utility library that simplifies working with PHP's Reflection API. This library provides a clean, intuitive interface for accessing and manipulating class properties, methods, and metadata regardless of their visibility.

## Features

- 🔍 **Property Access**: Read and modify private/protected properties
- 🛠️ **Method Inspection**: Access and call private/protected methods
- 📝 **DocBlock Analysis**: Extract and parse method documentation
- 🔄 **Parameter Details**: Get comprehensive method parameter information
- 🏗️ **Class Information**: Inspect class hierarchy, constants, and interfaces
- 🎯 **Type Safety**: Full type hinting and return type declarations

## Requirements

- PHP 7.4 or later

## Installation

Install via Composer:

```bash
composer require arraypress/reflector
```

## Basic Usage

```php
use ArrayPress\Utils\Reflector;

class Example {
    private $secretData = "hidden";
    private function secretMethod( $param) { return "Result: $param"; }
}

$example = new Example();

// Access private property
$value = Reflector::get_property( $example, 'secretData' );
// Result: "hidden"

// Call private method
$result = Reflector::call_method( $example, 'secretMethod', [ 'test' ] );
// Result: "Result: test"
```

## Property Operations

### Getting Properties

```php
// Get all properties (public, protected, private)
$properties = Reflector::get_properties( $example );

// Get only public properties
$public = Reflector::get_properties( $example, ReflectionProperty::IS_PUBLIC );

// Get single property
$value = Reflector::get_property( $example, 'propertyName' );

// Check property existence
$exists = Reflector::has_property( $example, 'propertyName' );
```

### Setting Properties

```php
// Set property value regardless of visibility
Reflector::set_property( $example, 'privateProperty', 'new value' );
```

## Method Operations

### Method Access

```php
// Get all methods
$methods = Reflector::get_methods( $example);

// Call any method
$result = Reflector::call_method( $example, 'methodName', ['param1', 'param2']);

// Check method existence
$exists = Reflector::has_method( $example, 'methodName' );
```

### Method Information

```php
// Get method parameters
$params = Reflector::get_method_parameters( $example, 'methodName' );
/* Returns:
[
    'paramName' => [
        'name' => 'paramName',
        'position' => 0,
        'type' => 'string',
        'type_allows_null' => false,
        'is_optional' => true,
        'has_default' => true,
        'default_value' => 'default',
        'is_variadic' => false,
        'is_passed_by_reference' => false,
        'doc_type' => 'string',
        'doc_description' => 'Parameter description from DocBlock'
    ]
]
*/

// Get method DocBlock
$docBlock = Reflector::get_method_docblock( $example, 'methodName' );

// Get method DocBlock without tags
$description = Reflector::get_method_docblock( $example, 'methodName', true );
```

## Class Information

```php
// Get class constants
$constants = Reflector::get_constants( $example );

// Get parent class
$parent = Reflector::get_parent_class( $example );

// Get implemented interfaces
$interfaces = Reflector::get_interfaces( $example );
```

## Error Handling

The library handles reflection errors gracefully:

- Property/method access methods return `null` or `false` on failure
- Collection methods (like `get_properties()`) return empty arrays on failure
- Methods throw `ReflectionException` only when explicitly documented

## Use Cases

- Unit Testing: Access private members for thorough testing
- Framework Development: Inspect and modify class behavior
- Legacy Code Integration: Work with poorly accessible code
- Debugging: Inspect object internals
- Meta-programming: Generate code based on class structure

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

- [Documentation](https://github.com/arraypress/reflector)
- [Issue Tracker](https://github.com/arraypress/reflector/issues)