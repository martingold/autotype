<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test;

use MartinGold\AutoType\Test\ValueObject\Email;
use MartinGold\AutoType\Test\ValueObject\PhoneNumber;
use MartinGold\AutoType\Test\ValueObject\Salary;
use MartinGold\AutoType\TypeDefinition\Provider\TypeDefinitionProvider;

final class MockTypeDefinitionProvider implements TypeDefinitionProvider
{
    public function get(): array
    {
        return [
            PhoneNumber::getDefinition(),
            Email::getDefinition(),
            Salary::getDefinition(),
        ];
    }
}
