<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\TypeDefinition;

use MartinGold\AutoType\Factory\AttributeTypeDefinitionFactory;
use MartinGold\AutoType\Test\ValueObject\PhoneNumber;
use MartinGold\AutoType\TypeDefinitionFinder\ClassTypeDefinitionFinder;
use PHPUnit\Framework\TestCase;

class DynamicTypeFinderTest extends TestCase
{
    public function testFindTypes(): void
    {
        $dynamicTypeFinder = new ClassTypeDefinitionFinder(__DIR__ . '/../ValueObject', [
            new AttributeTypeDefinitionFactory()
        ]);

        self::assertEquals(
            [PhoneNumber::getDefinition()],
            $dynamicTypeFinder->get(),
        );
    }

}