<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\TypeDefinition\Driver;

use MartinGold\AutoType\DynamicType\StringDynamicType;
use MartinGold\AutoType\Test\ValueObject\PhoneNumber;
use MartinGold\AutoType\TypeDefinition\Driver\AttributeTypeDefinitionDriver;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AttributeTypeDefinitionDriverTest extends TestCase
{
    public function testDriver(): void
    {
        $class = PhoneNumber::class;

        $reflection = new ReflectionClass($class);
        $driver = new AttributeTypeDefinitionDriver();

        self::assertTrue($driver->supports($reflection));
        self::assertEquals(StringDynamicType::class, $driver->getDynamicTypeClass($reflection));
        self::assertEquals('getValue', $driver->getValueMethodName($reflection));
        self::assertEquals('from', $driver->getConstructorMethodName($reflection));
    }
}
