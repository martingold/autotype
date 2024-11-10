<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\TypeDefinition\Provider;

use MartinGold\AutoType\Test\ValueObject\Email;
use MartinGold\AutoType\Test\ValueObject\PhoneNumber;
use MartinGold\AutoType\Test\ValueObject\Salary;
use MartinGold\AutoType\TypeDefinition\Driver\AttributeTypeDefinitionDriver;
use MartinGold\AutoType\TypeDefinition\Driver\InterfaceTypeDefinitionDriver;
use MartinGold\AutoType\TypeDefinition\Provider\ScanTypeDefinitionProvider;
use PHPUnit\Framework\TestCase;

class ScanTypeDefinitionFinderTest extends TestCase
{
    public function testFindTypes(): void
    {
        $dynamicTypeFinder = new ScanTypeDefinitionProvider(
            __DIR__ . '/../../ValueObject',
            [
                new AttributeTypeDefinitionDriver(),
                new InterfaceTypeDefinitionDriver(),
            ],
        );

        self::assertEqualsCanonicalizing([
            PhoneNumber::getDefinition(),
            Email::getDefinition(),
            Salary::getDefinition(),
        ], $dynamicTypeFinder->get());
    }
}
