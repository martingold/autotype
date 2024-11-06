<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinitionFinder;


use MartinGold\AutoType\TypeDefinition;

interface TypeDefinitionFinder
{
    /**
     * @return list<TypeDefinition>
     */
    public function get(): array;
}