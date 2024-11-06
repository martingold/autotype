<?php

declare(strict_types=1);

namespace MartinGold\AutoType;

use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\DBAL\Types\Type;
use LogicException;
use MartinGold\AutoType\TypeDefinitionFinder\TypeDefinitionFinder;

final readonly class DynamicTypeRegistry
{
    public function __construct(
        private TypeDefinitionFinder $dynamicTypeProvider,
    ) {
    }

    public function register(): void
    {
        $customTypes = $this->dynamicTypeProvider->get();

        foreach ($customTypes as $customType) {
            $typeClass = $customType->dynamicTypeClass;

            $type = $typeClass::create(
                $customType->valueObjectClass,
                $customType->getterMethodName,
                $customType->constructorMethodName,
            );

            $typeName = $customType->typeName;

            if (Type::hasType($typeName)) {
                continue;
            }

            try {
                Type::getTypeRegistry()->register($typeName, $type);
            } catch (DoctrineException $e) {
                throw new LogicException('Can not register type ' . $customType->typeName, 0, $e);
            }
        }
    }
}
