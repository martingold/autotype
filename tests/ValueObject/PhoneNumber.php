<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\ValueObject;

use MartinGold\AutoType\Attribute\Constructor;
use MartinGold\AutoType\Attribute\ValueGetter;
use MartinGold\AutoType\DynamicType\StringDynamicType;
use MartinGold\AutoType\TypeDefinition;

final readonly class PhoneNumber
{
    public function __construct(
        private string $number,
    ) {
    }

    #[ValueGetter]
    public function getValue(): string
    {
        return $this->number;
    }

    #[Constructor]
    public static function from(string $number): self
    {
        return new self($number);
    }

    public static function getDefinition(): TypeDefinition
    {
        return new TypeDefinition(
            dynamicTypeClass: StringDynamicType::class,
            typeName: PhoneNumber::class,
            valueObjectClass: PhoneNumber::class,
            getterMethodName: 'getValue',
            constructorMethodName: 'from',
        );
    }
}