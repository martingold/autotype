<?php

declare(strict_types=1);

namespace MartinGold\AutoType\DynamicType;

use Doctrine\DBAL\Types\Type;

interface DynamicType
{
    /**
     * @param class-string<object> $class
     */
    public static function create(string $class, string $getterMethodName, string|null $constructorMethodName = null): DynamicType&Type;
}
