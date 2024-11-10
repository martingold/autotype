<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\ValueObject;

use MartinGold\AutoType\Attribute\Constructor;
use MartinGold\AutoType\Attribute\ValueGetter;
use MartinGold\AutoType\DynamicType\IntegerDynamicType;
use MartinGold\AutoType\TypeDefinition\TypeDefinition;

final readonly class Salary
{
    /**
     * @param positive-int $amount
     */
    public function __construct(
        private int $amount,
    ) {
    }

    #[ValueGetter]
    public function getValue(): int
    {
        return $this->amount;
    }

    /**
     * @param positive-int $amount
     */
    #[Constructor]
    public static function from(int $amount): self
    {
        return new self($amount);
    }

    public static function getDefinition(): TypeDefinition
    {
        return new TypeDefinition(
            dynamicTypeClass: IntegerDynamicType::class,
            typeName: self::class,
            valueObjectClass: self::class,
            getterMethodName: 'getValue',
            constructorMethodName: 'from',
        );
    }
}
