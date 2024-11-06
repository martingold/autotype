<?php

declare(strict_types=1);

namespace MartinGold\AutoType;

/**
 * @template T of scalar|null
 */
interface ValueObject
{
    /**
     * @return T
     */
    public function getValue(): mixed;
}