<?php

declare(strict_types=1);

namespace MartinGold\AutoType;

use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\TypeRegistry;
use LogicException;
use MartinGold\AutoType\TypeDefinition\Provider\TypeDefinitionProvider;

final readonly class DynamicTypeRegistry
{
    private TypeDefinitionProvider $typeDefinitionProvider;
    private TypeRegistry $typeRegistry;

    public function __construct(
        TypeDefinitionProvider $typeDefinitionProvider,
        TypeRegistry|null $typeRegistry = null,
    ) {
        $this->typeDefinitionProvider = $typeDefinitionProvider;
        $this->typeRegistry = $typeRegistry ?? Type::getTypeRegistry();
    }

    public function register(): void
    {
        $customTypes = $this->typeDefinitionProvider->get();

        foreach ($customTypes as $customType) {
            $typeClass = $customType->dynamicTypeClass;

            $type = $typeClass::create(
                $customType->valueObjectClass,
                $customType->getterMethodName,
                $customType->constructorMethodName,
            );

            $typeName = $customType->typeName;

            if ($this->typeRegistry->has($typeName)) {
                continue;
            }

            try {
                $this->typeRegistry->register($typeName, $type);
            } catch (DoctrineException $e) {
                throw new LogicException('Can not register type ' . $customType->typeName, 0, $e);
            }
        }
    }
}
