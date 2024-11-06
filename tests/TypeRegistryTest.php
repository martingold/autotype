<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test;

use Doctrine\DBAL\Types\Type;
use MartinGold\AutoType\DynamicTypeRegistry;
use MartinGold\AutoType\Test\ValueObject\PhoneNumber;
use MartinGold\AutoType\TypeDefinition;
use MartinGold\AutoType\TypeDefinitionFinder\TypeDefinitionFinder;
use PHPUnit\Framework\TestCase;

class DynamicTypeRegistryTest extends TestCase
{
    public function testRegisterTypes(): void
    {
        $originalTypeCount = count(Type::getTypeRegistry()->getMap());

        $dynamicTypeProviderMock = new class implements TypeDefinitionFinder {

            /**
             * @return list<TypeDefinition>
             */
            public function get(): array
            {
                return [PhoneNumber::getDefinition()];
            }
        };

        (new DynamicTypeRegistry($dynamicTypeProviderMock))->register();

        self::assertEquals($originalTypeCount + 1, count(Type::getTypeRegistry()->getMap()));
    }

}