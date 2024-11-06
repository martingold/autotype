<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\TypeDefinition;

use MartinGold\AutoType\Factory\AttributeTypeDefinitionFactory;
use MartinGold\AutoType\Test\ValueObject\PhoneNumber;
use MartinGold\AutoType\TypeDefinitionFinder\CachedTypeDefinitionFinder;
use MartinGold\AutoType\TypeDefinitionFinder\ClassTypeDefinitionFinder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CachedDynamicTypeProviderTest extends TestCase
{
    public function testFindTypes(): void
    {
        $dynamicTypeFinder = new ClassTypeDefinitionFinder(dirname(__DIR__) . '/ValueObject', [
            new AttributeTypeDefinitionFactory()
        ]);

        $cache = new FilesystemAdapter(directory: sys_get_temp_dir());
        $cachedDynamicTypeProvider = new CachedTypeDefinitionFinder(
            typeDefinitionFinder: $dynamicTypeFinder,
            cache: $cache,
        );

        $cachedDynamicTypeProvider->get();
        $cachedDynamicTypeProvider->get();

        $cachedDefinitions = $cache->getItem('app.doctrine.dynamicTypes')->get();

        self::assertEquals(
            [PhoneNumber::getDefinition()],
            $cachedDefinitions,
        );
    }

}