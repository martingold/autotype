<?php

declare(strict_types=1);

namespace MartinGold\AutoType\DynamicType;

interface DynamicType
{
    /**
     * @param class-string<object> $class
     */
    public static function create(string $class, string $getterMethodName, string|null $constructorMethodName = null): self;
}
