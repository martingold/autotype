<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinitionFinder;

use League\ConstructFinder\ConstructFinder;
use MartinGold\AutoType\Factory\TypeDefinitionFactory;
use MartinGold\AutoType\TypeDefinition;
use ReflectionClass;

final readonly class ClassTypeDefinitionFinder implements TypeDefinitionFinder
{
    /**
     * @param non-empty-list<TypeDefinitionFactory> $dynamicTypeFactories
     */
    public function __construct(
        private string $sourceFolder,
        private array $dynamicTypeFactories,
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
            foreach ($this->dynamicTypeFactories as $dynamicTypeFactory) {
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
