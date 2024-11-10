<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\TypeDefinition\Provider;

use MartinGold\AutoType\Test\MockTypeDefinitionProvider;
use MartinGold\AutoType\Test\ValueObject\Email;
use MartinGold\AutoType\Test\ValueObject\PhoneNumber;
use MartinGold\AutoType\Test\ValueObject\Salary;
use MartinGold\AutoType\TypeDefinition\Provider\CachedTypeDefinitionProvider;
use MartinGold\AutoType\TypeDefinition\Provider\TypeDefinitionProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CachedDynamicTypeProviderTest extends TestCase
{
    public function testFindTypes(): void
    {
        $provider = new class implements TypeDefinitionProvider {
            private bool $accessed = false;

            public function get(): array
            {
                if ($this->accessed) {
                    return [];
                }

                $this->accessed = true;

                return (new MockTypeDefinitionProvider())->get();
            }
        };

        $cache = new ArrayAdapter();
        $cachedDynamicTypeProvider = new CachedTypeDefinitionProvider(
            typeDefinitionFinder: new $provider(),
            cache: $cache,
        );

        $emptyCacheItem = $cache->getItem('app.doctrine.dynamicTypes');
        self::assertFalse($emptyCacheItem->isHit());

        $cachedDynamicTypeProvider->get();

        $cacheItem = $cache->getItem('app.doctrine.dynamicTypes');
        self::assertTrue($cacheItem->isHit());

        self::assertEqualsCanonicalizing([
            Salary::getDefinition(),
            PhoneNumber::getDefinition(),
            Email::getDefinition(),
        ], $cacheItem->get());
    }
}
