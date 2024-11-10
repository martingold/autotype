<?php

declare(strict_types=1);

namespace MartinGold\AutoType\TypeDefinition\Provider;

use MartinGold\AutoType\TypeDefinition\TypeDefinition;
use Psr\Cache\CacheItemPoolInterface;

final readonly class CachedTypeDefinitionProvider implements TypeDefinitionProvider
{
    public function __construct(
        private TypeDefinitionProvider $typeDefinitionFinder,
        private CacheItemPoolInterface $cache,
        private string $cacheKey = 'app.doctrine.dynamicTypes',
    ) {
    }

    /**
     * @return list<TypeDefinition>
     */
    public function get(): array
    {
        $item = $this->cache->getItem($this->cacheKey);

        if ($item->get() === null) {
            $this->cache->save(
                $item->set($this->typeDefinitionFinder->get())
            );
        }

        /** @var list<TypeDefinition> $definitions */
        $definitions = $item->get();

        return $definitions;
    }
}
