<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Factory;

use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use MartinGold\AutoType\DynamicType\DynamicType;
use ReflectionClass;

interface TypeDefinitionFactory
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function supports(ReflectionClass $class): bool;

    /**
     * @param ReflectionClass<object> $class
     * @return class-string<DynamicType&Type>
     */
    public function getDynamicTypeClass(ReflectionClass $class): string;

    public function getValueMethodName(ReflectionClass $class): string;

    public function getConstructorMethodName(ReflectionClass $class): string|null;
}
