<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinition\Driver;

use Doctrine\DBAL\Types\Type;
use MartinGold\AutoType\Attribute\Constructor;
use MartinGold\AutoType\Attribute\ValueGetter;
use MartinGold\AutoType\DynamicType\DynamicType;
use MartinGold\AutoType\DynamicType\IntegerDynamicType;
use MartinGold\AutoType\DynamicType\StringDynamicType;
use MartinGold\AutoType\Exception\ShouldNotHappen;
use MartinGold\AutoType\Exception\UnsupportedType;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class AttributeTypeDefinitionDriver implements TypeDefinitionDriver
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function supports(ReflectionClass $class): bool
    {
        return $this->getMethodWithAttribute($class, ValueGetter::class) !== null;
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
            'int' => IntegerDynamicType::class,
            default => throw new UnsupportedType('Only string type is supported.'),
        };
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function getValueMethodName(ReflectionClass $class): string
    {
        return $this->getMethodWithAttribute($class, ValueGetter::class)?->getName()
            ?? throw new ShouldNotHappen('Should be covered by supports() method');
    }

    public function getConstructorMethodName(ReflectionClass $class): string|null
    {
        return $this->getMethodWithAttribute($class, Constructor::class)?->name;
    }

    /**
     * @param ReflectionClass<object> $class
     *
     * @throws UnsupportedType
     */
    private function getValueMethodReturnType(ReflectionClass $class): string
    {
        $method = $this->getMethodWithAttribute($class, ValueGetter::class)
            ?? throw new ShouldNotHappen('Should be covered by supports() method');

        $returnType = $method->getReturnType();

        if (!$returnType instanceof ReflectionNamedType) {
            throw new UnsupportedType("Intersection or union return type not supported in method {$class->getName()}::{$method->getName()}()");
        }

        if (!$returnType->isBuiltin()) {
            throw new UnsupportedType("Only scalar return types are supported in {$class->getName()}::{$method->getName()}()");
        }

        return $returnType->getName();
    }

    /**
     * @param ReflectionClass<object> $class
     * @param class-string<object> $attribute
     */
    private function getMethodWithAttribute(ReflectionClass $class, string $attribute): ReflectionMethod|null
    {
        foreach ($class->getMethods() as $method) {
            if ($method->getAttributes($attribute) !== []) {
                return $method;
            }
        }

        return null;
    }
}
