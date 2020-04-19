<?php declare(strict_types=1);

namespace Litea\DataTransfer;

use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionException;
//
use Litea\DataTransfer\Exceptions\UnknownPropertyTypeException;
use Litea\DataTransfer\Exceptions\MissingExpectedPropertyException;

/**
 * Class DataObject
 * @package Litea\DataTransfer
 */
abstract class DataObject
{
    public const DT_PROPERTY = '@dto-property';
    
    /**
     * @var bool
     * When set to false create() throws an
     * MissingExpectedPropertyException when one of
     * the dt-property is not present in the $data array.
     *
     * This applies only to non-initialized properties.
     * I.e. to properties, that do not have assigned value when declared.
     *
     * E.g.:
     * class Foo { public $a = "bar"; public $b; }
     * When property $a does not have value in data array nothing happens
     * as it's got default value.
     * Whereas if property $b does not have the value, MissingExpectedPropertyException is thrown.
     *
     */
    protected static $ignoreMissing = false;

    /**
     * @var bool
     * By default DataObject takes into account all properties matching
     * the visibility filter (@see https://www.php.net/manual/en/reflectionclass.getproperties)
     * except the static properties.
     *
     * The property name is used as a look-up key in source array unless
     * dt-property is specified in property's doc comment with the value of the different
     * key name.
     *
     * Set this property to false if you want to include only properties
     * marked with the dt-property doc comment value.
     */
    protected static $allowImplicit = true;

    /**
     * @var int
     * Filter that determines what properties get included.
     */
    protected static $propertyFilter = ReflectionProperty::IS_PUBLIC;

    /**
     * @param array $data
     * @return static
     * @throws ReflectionException
     * @throws MissingExpectedPropertyException
     */
    public static function create(array $data = [])
    {
        $dto = new static();
        $properties = self::getProperties();

        foreach ($properties as $property) {
            $name = $property->getName();
            $key = $property->getKey();
            $isInData = array_key_exists($key, $data);

            if ($isInData) {
                $value = $property->parseValue($data[$key]);
                $setter = 'set' . ucfirst($name);

                if (method_exists($dto, $setter)) {
                    $dto->$setter($value);
                } else {
                    $dto->$name = $value;
                }

                continue;
            }

            if ($property->isInitialized($dto) && $dto->$name !== null) {
                continue;
            }

            if (!static::$ignoreMissing) {
                throw new MissingExpectedPropertyException(sprintf(
                    'Data array does not contain key %s for mandatory' .
                    ' property %s',
                    $key,
                    $name
                ));
            }
        }

        return $dto;
    }

    /**
     * @return array|DataProperty[]
     * @throws ReflectionException
     */
    protected static function getProperties(): array
    {
        $reflection = new ReflectionClass(static::class);

        /** @var DataProperty[] $properties */
        $properties = [];

        $reflectionProperties = $reflection->getProperties(self::$propertyFilter);

        $pattern = sprintf(
            '/%s\s?(\w*)/',
            preg_quote(self::DT_PROPERTY)
        );

        foreach ($reflectionProperties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $docBlock = (string)$property->getDocComment();

            if (preg_match($pattern, $docBlock, $matches)) {
                $key = trim($matches[1]) !== ''
                    ? $matches[1]
                    : $property->getName();
            } elseif (static::$allowImplicit) {
                $key = $property->getName();
            } else {
                continue;
            }

            $properties[] = new DataProperty(
                $property,
                $key
            );
        }

        return $properties;
    }
}