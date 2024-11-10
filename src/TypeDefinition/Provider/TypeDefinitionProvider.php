<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinition\Provider;

use MartinGold\AutoType\TypeDefinition\TypeDefinition;

interface TypeDefinitionProvider
{
    /**
     * @return list<TypeDefinition>
     */
    public function get(): array;
}
