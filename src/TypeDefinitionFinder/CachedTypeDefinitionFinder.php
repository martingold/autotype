<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinitionFinder;

use MartinGold\AutoType\DynamicType\TypeDefinition;
use Psr\Cache\CacheItemPoolInterface;

final readonly class CachedTypeDefinitionFinder implements TypeDefinitionFinder
{
    private const string CACHE_KEY = 'app.doctrine.dynamicTypes';

    public function __construct(
        private TypeDefinitionFinder $typeDefinitionFinder,
        private CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * @return list<\MartinGold\AutoType\TypeDefinition>
     */
    public function get(): array
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        $typeDefinitions = $cacheItem->get();
        if ($typeDefinitions === null) {
            $cacheItem->set($this->typeDefinitionFinder->get());
            $this->cache->save($cacheItem);
        }

        return $cacheItem->get();
    }
}