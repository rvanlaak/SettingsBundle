<?php

namespace Dmishh\SettingsBundle\Tests;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
    protected EntityManager $em;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->em = $this->createEntityManager();
        $this->generateSchema();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->em->close();
    }

    protected function createEntityManager(): EntityManager
    {
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('EntityProxy');
        $config->setAutoGenerateProxyClasses(true);

        $driver = new \Doctrine\ORM\Mapping\Driver\AttributeDriver(
            [__DIR__.'/../Entity']
        );
        $config->setMetadataDriverImpl($driver);

        $conn = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        return EntityManager::create($conn, $config);
    }

    protected function generateSchema(): void
    {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();

        if (!empty($metadatas)) {
            $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $tool->dropSchema($metadatas);
            $tool->createSchema($metadatas);
        }
    }
}
