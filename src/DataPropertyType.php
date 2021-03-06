<?php

declare(strict_types=1);

namespace Litea\DataTransfer;

use Exception;
use ReflectionType;

class DataPropertyType
{
    /**
     * @var ReflectionType|null
     */
    private $type;

    /**
     * @var string|null
     */
    private $docComment;

    /**
     * DataPropertyType constructor.
     * @param ReflectionType $type
     * @param string|null $docComment
     */
    public function __construct(?ReflectionType $type = null, ?string $docComment = null)
    {
        $this->type = $type;
        $this->docComment = $docComment;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        $types = $this->getTypes();

        $allowsNull = $this->type === null
            ? false
            : $this->type->allowsNull();

        return $allowsNull || in_array('null', $types);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function parseValue($value)
    {
        $types = $this->getTypes();

        foreach ($types as $type) {
            try {
                return $this->convertToType($value, $type);
            } catch (\Exception $exception) {
            }
        }

        if ($this->type !== null && $this->type->isBuiltin()) {
            settype($value, $this->type->getName());
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return mixed
     * @throws Exception
     */
    protected function convertToType($value, string $type)
    {
        switch ($type) {
            case 'string':
                return (string)$value;

            case 'int':
            case 'integer':
                return (int)$value;

            case 'float':
            case 'double':
                return (float)$value;

            case 'bool':
            case 'boolean':
                return (bool)$value;
        }

        if (strtolower($type) === 'array' || substr($type, -2) === '[]') {
            if (!is_array($value)) {
                throw new Exception('Can not convert non-array value to array');
            }

            $arrayType = substr($type, 0, -2);

            if (class_exists($arrayType) && is_subclass_of($arrayType, DataObject::class)) {
                return array_map(function ($item) use ($arrayType) {
                    $callback = [$arrayType, 'create'];

                    if (is_callable($callback)) {
                        return call_user_func($callback, $item);
                    }

                    throw new Exception('Method not found');
                }, $value);
            }

            return $value;
        }

        $callback = [$type, 'create'];
        if (class_exists($type) && is_subclass_of($type, DataObject::class) && is_callable($callback)) {
            return call_user_func($callback, [$value]);
        }

        throw new Exception('No suitable type conversion found');
    }

    /**
     * @return string[]
     */
    protected function getTypes(): array
    {
        if (preg_match('/@var ((?:(?:[\w?|\\\\<>])+(?:\[])?)+)/', $this->docComment ?? '', $matches)) {
            return explode('|', $matches[1]);
        }

        return [];
    }
}
