<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinition\Driver;

use Doctrine\DBAL\Types\Type;
use MartinGold\AutoType\DynamicType\DynamicType;
use ReflectionClass;

interface TypeDefinitionDriver
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function supports(ReflectionClass $class): bool;

    /**
     * @param ReflectionClass<object> $class
     *
     * @return class-string<DynamicType&Type>
     */
    public function getDynamicTypeClass(ReflectionClass $class): string;

    /**
     * @param ReflectionClass<object> $class
     */
    public function getValueMethodName(ReflectionClass $class): string;

    /**
     * @param ReflectionClass<object> $class
     */
    public function getConstructorMethodName(ReflectionClass $class): string|null;
}
