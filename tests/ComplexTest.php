<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test;

use Doctrine\ORM\Tools\SchemaTool;
use MartinGold\AutoType\DynamicTypeRegistry;
use MartinGold\AutoType\Test\Entity\Employee;
use MartinGold\AutoType\Test\ValueObject\Email;
use MartinGold\AutoType\Test\ValueObject\PhoneNumber;
use MartinGold\AutoType\Test\ValueObject\Rating;
use MartinGold\AutoType\Test\ValueObject\Salary;
use MartinGold\AutoType\TypeDefinition\Provider\DefaultTypeDefinitionProvider;
use PHPUnit\Framework\TestCase;

class ComplexTest extends TestCase
{
    public function testRegisterTypes(): void
    {
        (new DynamicTypeRegistry(
            new DefaultTypeDefinitionProvider(__DIR__ . '/ValueObject'),
        ))->register();

        $entityManager = DoctrineSetup::getEntityManager();

        $phoneNumber = '+420 777 555 666';
        $email = 'info@employee.tld';
        $salary = 44100;
        $rating = 2.1234567891234;
        $employee = new Employee(
            'Test employee',
            new PhoneNumber($phoneNumber),
            new Email($email),
            new Salary($salary),
            new Rating($rating)
        );

        $entityManager->persist($employee);
        $entityManager->flush();
        $entityManager->clear();

        $employee = $entityManager->find(Employee::class, $employee->getId());

        self::assertNotNull($employee);

        self::assertEquals(
            new PhoneNumber($phoneNumber),
            $employee->getPhoneNumber(),
        );

        self::assertEquals(
            new Salary($salary),
            $employee->getSalary(),
        );

        self::assertEquals(
            new Rating($rating),
            $employee->getRating(),
        );
    }

    public function testDatabaseColumnTypes(): void
    {
        (new DynamicTypeRegistry(
            new DefaultTypeDefinitionProvider(__DIR__ . '/ValueObject')
        ))->register();

        $entityManager = DoctrineSetup::getEntityManager();

        $employeeSQL = (new SchemaTool($entityManager))->getCreateSchemaSql([
            $entityManager->getClassMetadata(Employee::class),
        ])[0];

        $expectedSQL = 'CREATE TABLE Employee (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, phoneNumber VARCHAR NOT NULL, email VARCHAR NOT NULL, salary INTEGER NOT NULL, rating NUMERIC(4, 15) NOT NULL)';

        self::assertEquals($expectedSQL, $employeeSQL);
    }
}
