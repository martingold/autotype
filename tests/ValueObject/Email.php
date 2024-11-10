<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\ValueObject;

use MartinGold\AutoType\DynamicType\StringDynamicType;
use MartinGold\AutoType\TypeDefinition\TypeDefinition;
use MartinGold\AutoType\ValueObject;

/**
 * @implements ValueObject<string>
 */
final readonly class Email implements ValueObject
{
    public function __construct(
        private string $email,
    ) {
    }

    public function getValue(): string
    {
        return $this->email;
    }

    /**
     * @param string $value
     */
    public static function create(mixed $value): self
    {
        return new self($value);
    }

    public static function getDefinition(): TypeDefinition
    {
        return new TypeDefinition(
            dynamicTypeClass: StringDynamicType::class,
            typeName: self::class,
            valueObjectClass: self::class,
            getterMethodName: 'getValue',
            constructorMethodName: 'create',
        );
    }
}
