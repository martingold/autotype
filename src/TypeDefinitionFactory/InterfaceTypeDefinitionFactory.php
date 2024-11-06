<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Factory;

use Doctrine\DBAL\Types\Type;
use MartinGold\AutoType\Attribute\Constructor;
use MartinGold\AutoType\Attribute\ValueGetter;
use MartinGold\AutoType\DynamicType\DynamicType;
use MartinGold\AutoType\DynamicType\StringDynamicType;
use MartinGold\AutoType\Exception\ShouldNotHappen;
use MartinGold\AutoType\Exception\UnsupportedType;
use MartinGold\AutoType\ValueObject;
use ReflectionClass;

use ReflectionMethod;
use ReflectionNamedType;

class InterfaceTypeDefinitionFactory implements TypeDefinitionFactory
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function supports(ReflectionClass $class): bool
    {
        return $class->isSubclassOf(ValueObject::class);
    }

    /**
     * @param ReflectionClass<object> $class
     *
     * @return class-string<DynamicType&Type>
     */
    public function getDynamicTypeClass(ReflectionClass $class): string
    {
        return match ($this->getValueMethodReturnType($class)) {
            'string' => StringDynamicType::class,
            default => throw new UnsupportedType('Only string type is supported.')
        };
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function getValueMethodName(ReflectionClass $class): string
    {
        return 'getValue';
    }

    public function getConstructorMethodName(ReflectionClass $class): string|null
    {
        return null;
    }

    /**
     * @param ReflectionClass<object> $class
     */
    private function getValueMethodReturnType(ReflectionClass $class): string
    {
        $returnType = $class->getMethod('getValue')->getReturnType();

        if (!$returnType instanceof ReflectionNamedType) {
            throw new UnsupportedType("Intersection or union return type not supported in method {$class->getName()}::{$method->getName()}()");
        }

        if (!$returnType->isBuiltin()) {
            throw new UnsupportedType("Only scalar return types are supported in {$class->getName()}::{$method->getName()}()");
        }

        return $returnType->getName();
    }
}
