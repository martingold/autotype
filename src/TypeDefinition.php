<?php

declare(strict_types=1);

namespace MartinGold\AutoType;

use Doctrine\DBAL\Types\Type;
use MartinGold\AutoType\DynamicType\DynamicType;

/**
 * @phpstan-type TypeDefinition array{
 *      dynamicTypeClass: class-string<Type>,
 *      typeName: string,
 *      valueObjectClass: class-string<object>,
 *      constructorMethodName: string|null,
 *  }
 */
final readonly class TypeDefinition
{
    /**
     * @param class-string<DynamicType&Type> $dynamicTypeClass
     * @param class-string<object> $valueObjectClass
     */
    public function __construct(
        public string $dynamicTypeClass,
        public string $typeName,
        public string $valueObjectClass,
        public string $getterMethodName,
        public string|null $constructorMethodName
    ) {
    }

    /**
     * @return TypeDefinition
     */
    public function toArray(): array
    {
        return [
            'dynamicTypeClass' => $this->dynamicTypeClass,
            'typeName' => $this->typeName,
            'valueObjectClass' => $this->valueObjectClass,
            'getterMethodName' => $this->getterMethodName,
            'constructorMethodName' => $this->constructorMethodName,
        ];
    }

    /**
     * @return TypeDefinition
     */
    public function __serialize(): array
    {
        return $this->toArray();
    }

    public function fromArray(array $data): self
    {
        return new self(
            dynamicTypeClass: $data['dynamicTypeClass'],
            typeName: $data['typeName'],
            valueObjectClass: $data['valueObjectClass'],
            getterMethodName: $data['getterMethodName'],
            constructorMethodName: $data['constructorMethodName'],
        );
    }

    /**
     * @param TypeDefinition $data
     */
    public function __unserialize(array $data): void
    {
        $this->dynamicTypeClass = $data['dynamicTypeClass'];
        $this->typeName = $data['typeName'];
        $this->valueObjectClass = $data['valueObjectClass'];
        $this->getterMethodName = $data['getterMethodName'];
        $this->constructorMethodName = $data['constructorMethodName'];
    }
}