<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinition;

use Doctrine\DBAL\Types\Type;
use MartinGold\AutoType\DynamicType\DynamicType;

/**
 * @phpstan-type TypeDefinitionShape array{
 *      dynamicTypeClass: class-string<DynamicType&Type>,
 *      typeName: string,
 *      valueObjectClass: class-string<object>,
 *      getterMethodName: string,
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
        public string|null $constructorMethodName,
    ) {
    }

    /**
     * @return TypeDefinitionShape
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
     * @param TypeDefinitionShape $data
     */
    public static function fromArray(array $data): self
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
     * @return TypeDefinitionShape
     */
    public function __serialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param TypeDefinitionShape $data
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
