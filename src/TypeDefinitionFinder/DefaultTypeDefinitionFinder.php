<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinitionFinder;

use MartinGold\AutoType\Factory\AttributeTypeDefinitionFactory;
use MartinGold\AutoType\TypeDefinition;

final readonly class DefaultTypeDefinitionFinder implements TypeDefinitionFinder
{
    private ClassTypeDefinitionFinder $wrappedDynamicTypeFinder;

    public function __construct(
        string $sourceFolder,
    ) {
        $this->wrappedDynamicTypeFinder = new ClassTypeDefinitionFinder($sourceFolder, [
            new AttributeTypeDefinitionFactory(),
        ]);
    }

    /**
     * @return list<TypeDefinition>
     */
    public function get(): array
    {
       return $this->wrappedDynamicTypeFinder->get();
    }
}
