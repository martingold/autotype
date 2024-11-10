<?php

declare(strict_types=1);

namespace MartinGold\AutoType\Test;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use LogicException;
use MartinGold\AutoType\Test\Entity\Employee;

final class DoctrineSetup
{
    public static function getEntityManager(): EntityManagerInterface
    {
        $paths = [__DIR__ . '/Entity'];

        $dbParams = ['driver' => 'pdo_sqlite', 'memory' => true];

        $config = ORMSetup::createAttributeMetadataConfiguration($paths, true);
        $connection = DriverManager::getConnection($dbParams, $config);
        $entityManager = new EntityManager($connection, $config);

        $schemaTool = new SchemaTool($entityManager);
        try {
            $schemaTool->createSchema(
                [$entityManager->getClassMetadata(Employee::class)],
            );
        } catch (ToolsException $exception) {
            throw new LogicException($exception->getMessage(), 0, $exception);
        }

        return $entityManager;
    }
}
