<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinition\Provider;

use League\ConstructFinder\ConstructFinder;
use MartinGold\AutoType\TypeDefinition\Driver\TypeDefinitionDriver;
use MartinGold\AutoType\TypeDefinition\TypeDefinition;
use ReflectionClass;

final readonly class ScanTypeDefinitionProvider implements TypeDefinitionProvider
{
    /**
     * @param list<TypeDefinitionDriver> $typeDefinitionDrivers
     */
    public function __construct(
        private string $sourceFolder,
        private array $typeDefinitionDrivers,
    ) {
    }

    /**
     * @return list<TypeDefinition>
     */
    public function get(): array
    {
        $classNames = ConstructFinder::locatedIn($this->sourceFolder)->findClassNames();

        $definitions = [];

        foreach ($classNames as $className) {
            $class = new ReflectionClass($className);

            $factory = null;
            foreach ($this->typeDefinitionDrivers as $dynamicTypeFactory) {
                if (!$dynamicTypeFactory->supports($class)) {
                    continue;
                }
                $factory = $dynamicTypeFactory;
            }

            if ($factory === null) {
                continue;
            }

            $definitions[] = new TypeDefinition(
                dynamicTypeClass: $factory->getDynamicTypeClass($class),
                typeName: $class->getName(),
                valueObjectClass: $class->getName(),
                getterMethodName: $factory->getValueMethodName($class),
                constructorMethodName: $factory->getConstructorMethodName($class),
            );
        }

        return $definitions;
    }
}
