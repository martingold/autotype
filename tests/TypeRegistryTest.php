<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test;

use Doctrine\DBAL\Types\TypeRegistry;
use MartinGold\AutoType\DynamicTypeRegistry;
use PHPUnit\Framework\TestCase;

class TypeRegistryTest extends TestCase
{
    public function testRegisterTypes(): void
    {
        $typeRegistry = new TypeRegistry();

        (new DynamicTypeRegistry(
            new MockTypeDefinitionProvider(),
            $typeRegistry,
        ))->register();

        self::assertCount(4, $typeRegistry->getMap());
    }
}
