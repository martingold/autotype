<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use LogicException;
use MartinGold\AutoType\Test\ValueObject\Email;
use MartinGold\AutoType\Test\ValueObject\PhoneNumber;
use MartinGold\AutoType\Test\ValueObject\Rating;
use MartinGold\AutoType\Test\ValueObject\Salary;

#[Entity]
class Employee
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    // @phpstan-ignore property.unusedType
    private int|null $id = null;

    #[Column]
    private string $name;

    #[Column(type: PhoneNumber::class)]
    private PhoneNumber $phoneNumber;

    #[Column(type: Email::class)]
    private Email $email;

    #[Column(type: Salary::class)]
    private Salary $salary;

    #[Column(type: Rating::class, precision: 4, scale: 15)]
    private Rating $rating;

    public function __construct(
        string $name,
        PhoneNumber $phoneNumber,
        Email $email,
        Salary $salary,
        Rating $rating,
    ) {
        $this->name = $name;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->salary = $salary;
        $this->rating = $rating;
    }

    public function getId(): int
    {
        if ($this->id === null) {
            throw new LogicException('Cannot access id on unpersisted entity.');
        }

        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getSalary(): Salary
    {
        return $this->salary;
    }

    public function getRating(): Rating
    {
        return $this->rating;
    }
}
