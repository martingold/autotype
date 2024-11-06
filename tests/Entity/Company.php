<?php

declare(strict_types=1);

namespace MartingGold\AutoType\Test\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use MartinGold\AutoType\Tests\ValueObject\PhoneNumber;

#[Entity]
class Company
{
    #[Column]
    private string $name;

    #[Column(type: Url::class)]
    private Url $url;
}