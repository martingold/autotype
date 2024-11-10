<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinition\Provider;

use MartinGold\AutoType\TypeDefinition\Driver\AttributeTypeDefinitionDriver;
use MartinGold\AutoType\TypeDefinition\Driver\InterfaceTypeDefinitionDriver;
use MartinGold\AutoType\TypeDefinition\TypeDefinition;

final readonly class DefaultTypeDefinitionProvider implements TypeDefinitionProvider
{
    private TypeDefinitionProvider $wrappedDynamicTypeFinder;

    public function __construct(
        string $sourceFolder,
    ) {
        $this->wrappedDynamicTypeFinder = new ScanTypeDefinitionProvider($sourceFolder, [
            new AttributeTypeDefinitionDriver(),
            new InterfaceTypeDefinitionDriver(),
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
