<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\ValueObject;

use MartinGold\AutoType\DynamicType\FloatDynamicType;
use MartinGold\AutoType\TypeDefinition\TypeDefinition;
use MartinGold\AutoType\ValueObject;

/**
 * @implements ValueObject<float>
 */
final readonly class Rating implements ValueObject
{
    public function __construct(
        private float $rating,
    ) {
    }

    public function getValue(): float
    {
        return $this->rating;
    }

    /**
     * @param float $value
     */
    public static function create(mixed $value): self
    {
        return new self($value);
    }

    public static function getDefinition(): TypeDefinition
    {
        return new TypeDefinition(
            dynamicTypeClass: FloatDynamicType::class,
            typeName: self::class,
            valueObjectClass: self::class,
            getterMethodName: 'getValue',
            constructorMethodName: 'create',
        );
    }
}
