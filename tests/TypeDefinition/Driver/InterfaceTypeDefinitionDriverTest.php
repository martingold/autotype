<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\TypeDefinition\Driver;

use MartinGold\AutoType\DynamicType\StringDynamicType;
use MartinGold\AutoType\Test\ValueObject\Email;
use MartinGold\AutoType\TypeDefinition\Driver\InterfaceTypeDefinitionDriver;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class InterfaceTypeDefinitionDriverTest extends TestCase
{
    public function testDriver(): void
    {
        $class = Email::class;

        $reflection = new ReflectionClass($class);
        $driver = new InterfaceTypeDefinitionDriver();

        self::assertTrue($driver->supports($reflection));
        self::assertEquals(StringDynamicType::class, $driver->getDynamicTypeClass($reflection));
        self::assertEquals('getValue', $driver->getValueMethodName($reflection));
        self::assertEquals('create', $driver->getConstructorMethodName($reflection));
    }
}
