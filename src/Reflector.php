<?php
/**
 * A utility class providing simplified access to PHP's Reflection functionality.
 *
 * This class offers a streamlined interface for common reflection operations including:
 * - Accessing and modifying private/protected properties
 * - Inspecting and calling methods regardless of visibility
 * - Retrieving class constants and interface information
 * - Examining method parameters and class hierarchies
 *
 * @package     ArrayPress/Utils
 * @copyright   Copyright 2024, ArrayPress Limited
 * @license     GPL-2.0-or-later
 * @version     1.0.0
 * @author      David Sherlock
 *
 * @link        https://www.php.net/manual/en/book.reflection.php PHP Reflection
 */

declare( strict_types=1 );

namespace ArrayPress\Utils;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

class Reflector {

	/**
	 * Get reflected properties from an object or class.
	 *
	 * @param object|string $object_or_class The object or class name.
	 * @param int           $filter          Optional. Filter for property types (default: all).
	 *
	 * @return array The array of property names and their values.
	 * @throws ReflectionException If the class cannot be reflected.
	 */
	public static function get_properties( $object_or_class, int $filter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE ): array {
		$properties = [];
		if ( is_object( $object_or_class ) || class_exists( $object_or_class ) ) {
			$reflectedClass      = new ReflectionClass( $object_or_class );
			$reflectedProperties = $reflectedClass->getProperties( $filter );
			foreach ( $reflectedProperties as $property ) {
				$propertyName = $property->getName();
				$property->setAccessible( true );
				if ( is_object( $object_or_class ) && isset( $object_or_class->{$propertyName} ) ) {
					$propertyValue               = $property->getValue( $object_or_class );
					$properties[ $propertyName ] = $propertyValue;
				} elseif ( is_string( $object_or_class ) && $property->isStatic() ) {
					$propertyValue               = $property->getValue();
					$properties[ $propertyName ] = $propertyValue;
				}
			}
		}

		return $properties;
	}

	/**
	 * Get a reflected property from an object or class.
	 *
	 * @param object|string $object_or_class The object or class name.
	 * @param string        $propertyName    The name of the property.
	 *
	 * @return mixed|null The value of the property, or null if it doesn't exist.
	 */
	public static function get_property( $object_or_class, string $propertyName ) {
		try {
			$reflectedClass = new ReflectionClass( $object_or_class );
			$property       = $reflectedClass->getProperty( $propertyName );
			$property->setAccessible( true );

			return $property->getValue( is_object( $object_or_class ) ? $object_or_class : null );
		} catch ( ReflectionException $e ) {
			return null;
		}
	}

	/**
	 * Set a reflected property on an object or class.
	 *
	 * @param object|string $object_or_class The object or class name.
	 * @param string        $propertyName    The name of the property.
	 * @param mixed         $value           The value to set.
	 *
	 * @return bool True if successful, false otherwise.
	 */
	public static function set_property( $object_or_class, string $propertyName, $value ): bool {
		try {
			$reflectedClass = new ReflectionClass( $object_or_class );
			$property       = $reflectedClass->getProperty( $propertyName );
			$property->setAccessible( true );
			$property->setValue( is_object( $object_or_class ) ? $object_or_class : null, $value );

			return true;
		} catch ( ReflectionException $e ) {
			return false;
		}
	}

	/**
	 * Get reflected methods from an object or class.
	 *
	 * @param object|string $object_or_class The object or class name.
	 * @param int           $filter          Optional. Filter for method types (default: all).
	 *
	 * @return array The array of method names and their ReflectionMethod objects.
	 * @throws ReflectionException If the class cannot be reflected.
	 */
	public static function get_methods( $object_or_class, int $filter = ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE ): array {
		$methods = [];
		if ( is_object( $object_or_class ) || class_exists( $object_or_class ) ) {
			$reflectedClass   = new ReflectionClass( $object_or_class );
			$reflectedMethods = $reflectedClass->getMethods( $filter );
			foreach ( $reflectedMethods as $method ) {
				$methods[ $method->getName() ] = $method;
			}
		}

		return $methods;
	}

	/**
	 * Call a reflected method on an object or class.
	 *
	 * @param object|string $object_or_class The object or class name.
	 * @param string        $method_name     The name of the method.
	 * @param array         $parameters      Optional. The parameters to pass to the method.
	 *
	 * @return mixed The result of the method call.
	 * @throws ReflectionException If the method cannot be reflected or called.
	 */
	public static function call_method( $object_or_class, string $method_name, array $parameters = [] ) {
		$reflectedClass = new ReflectionClass( $object_or_class );
		$method         = $reflectedClass->getMethod( $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( is_object( $object_or_class ) ? $object_or_class : null, $parameters );
	}

	/**
	 * Check if a property exists in an object or class.
	 *
	 * @param object|string $object_or_class The object or class name.
	 * @param string        $propertyName    The name of the property.
	 *
	 * @return bool True if the property exists, false otherwise.
	 */
	public static function has_property( $object_or_class, string $propertyName ): bool {
		try {
			$reflectedClass = new ReflectionClass( $object_or_class );

			return $reflectedClass->hasProperty( $propertyName );
		} catch ( ReflectionException $e ) {
			return false;
		}
	}

	/**
	 * Check if a method exists in an object or class.
	 *
	 * @param object|string $object_or_class The object or class name.
	 * @param string        $methodName      The name of the method.
	 *
	 * @return bool True if the method exists, false otherwise.
	 */
	public static function has_method( $object_or_class, string $methodName ): bool {
		try {
			$reflectedClass = new ReflectionClass( $object_or_class );

			return $reflectedClass->hasMethod( $methodName );
		} catch ( ReflectionException $e ) {
			return false;
		}
	}

	/**
	 * Get class constants.
	 *
	 * @param object|string $object_or_class The object or class name.
	 *
	 * @return array Array of constants and their values.
	 */
	public static function get_constants( $object_or_class ): array {
		try {
			$reflectedClass = new ReflectionClass( $object_or_class );

			return $reflectedClass->getConstants();
		} catch ( ReflectionException $e ) {
			return [];
		}
	}

	/**
	 * Get parent class.
	 *
	 * @param object|string $object_or_class The object or class name.
	 *
	 * @return string|null Parent class name or null if no parent.
	 */
	public static function get_parent_class( $object_or_class ): ?string {
		try {
			$reflectedClass = new ReflectionClass( $object_or_class );
			$parentClass    = $reflectedClass->getParentClass();

			return $parentClass ? $parentClass->getName() : null;
		} catch ( ReflectionException $e ) {
			return null;
		}
	}

	/**
	 * Get class interfaces.
	 *
	 * @param object|string $object_or_class The object or class name.
	 *
	 * @return array Array of interface names.
	 */
	public static function get_interfaces( $object_or_class ): array {
		try {
			$reflectedClass = new ReflectionClass( $object_or_class );

			return array_keys( $reflectedClass->getInterfaces() );
		} catch ( ReflectionException $e ) {
			return [];
		}
	}

	/**
	 * Get detailed method parameter information including DocBlock comments.
	 *
	 * @param object|string $object_or_class The object or class name.
	 * @param string        $methodName      The name of the method.
	 *
	 * @return array Array of detailed parameter information.
	 */
	public static function get_method_parameters( $object_or_class, string $methodName ): array {
		try {
			$reflectedClass = new ReflectionClass( $object_or_class );
			$method         = $reflectedClass->getMethod( $methodName );
			$docComment     = $method->getDocComment();
			$parameters     = [];
			$position       = 0;

			// Parse DocBlock @param tags if they exist
			$paramDocs = [];
			if ( $docComment ) {
				preg_match_all( '/@param\s+([^\s]+)\s+\$([^\s]+)\s*([^\n]+)?/', $docComment, $matches, PREG_SET_ORDER );
				foreach ( $matches as $match ) {
					$paramDocs[ $match[2] ] = [
						'type'        => $match[1],
						'description' => isset( $match[3] ) ? trim( $match[3] ) : ''
					];
				}
			}

			foreach ( $method->getParameters() as $param ) {
				$name                = $param->getName();
				$parameters[ $name ] = [
					'name'                   => $name,
					'position'               => $position ++,
					'type'                   => $param->getType() ? $param->getType()->getName() : null,
					'type_allows_null'       => ! $param->getType() || $param->getType()->allowsNull(),
					'is_optional'            => $param->isOptional(),
					'has_default'            => $param->isDefaultValueAvailable(),
					'default_value'          => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
					'is_variadic'            => $param->isVariadic(),
					'is_passed_by_reference' => $param->isPassedByReference(),
					'doc_type'               => $paramDocs[ $name ]['type'] ?? null,
					'doc_description'        => $paramDocs[ $name ]['description'] ?? null
				];
			}

			return $parameters;
		} catch ( ReflectionException $e ) {
			return [];
		}
	}

	/**
	 * Get method DocBlock comment.
	 *
	 * @param object|string $object_or_class The object or class name.
	 * @param string        $methodName      The name of the method.
	 * @param bool          $strip_tags      Optional. Whether to strip DocBlock tags. Default false.
	 *
	 * @return string|null The DocBlock comment or null if not found.
	 */
	public static function get_method_docblock( $object_or_class, string $methodName, bool $strip_tags = false ): ?string {
		try {
			$reflectedClass = new ReflectionClass( $object_or_class );
			$method         = $reflectedClass->getMethod( $methodName );
			$docComment     = $method->getDocComment();

			if ( $docComment === false ) {
				return null;
			}

			if ( $strip_tags ) {
				// Remove DocBlock start/end markers
				$docComment = preg_replace( '/^\s*\/\*\*|\*\/\s*$/', '', $docComment );
				// Remove asterisks at the start of lines
				$docComment = preg_replace( '/^\s*\*\s*/m', '', $docComment );
				// Remove @tags and their content
				$docComment = preg_replace( '/@\w+\s+[^\n]+/', '', $docComment );
				// Clean up extra whitespace
				$docComment = trim( preg_replace( '/\s+/', ' ', $docComment ) );
			}

			return $docComment;
		} catch ( ReflectionException $e ) {
			return null;
		}
	}

}