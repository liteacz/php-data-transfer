<?php

declare(strict_types=1);

namespace Litea\DataTransfer;

use ReflectionProperty;
//
use Litea\DataTransfer\Exceptions\UnknownPropertyTypeException;

class DataProperty
{
    /**
     * @var ReflectionProperty
     */
    protected $reflection;

    /**
     * @var string
     */
    protected $key;

    public function __construct(ReflectionProperty $reflection, string $key = null)
    {
        $this->reflection = $reflection;
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return DataPropertyType
     */
    public function getType(): DataPropertyType
    {
        $type = $this->reflection->getType();
        $docComment = $this->reflection->getDocComment();

        return new DataPropertyType(
            $type === false ? null : $type,
            $docComment === false ? null : $docComment
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->reflection->getName();
    }

    /**
     * @param $value
     * @return mixed
     */
    public function parseValue($value)
    {
        return $this->getType()->parseValue($value);
    }

    /**
     * @param $object
     * @return bool
     */
    public function isInitialized($object): bool
    {
        return $this->reflection->isInitialized($object);
    }
}
