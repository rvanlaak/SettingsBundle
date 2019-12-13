<?php

namespace Dmishh\SettingsBundle\Tests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Configuration;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
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

    protected function createEntityManager()
    {
        $config = new Configuration();
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('EntityProxy');
        $config->setAutoGenerateProxyClasses(true);

        AnnotationRegistry::registerFile(
            __DIR__.
            '/../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
        );
        $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(
            new \Doctrine\Common\Annotations\AnnotationReader(),
            [__DIR__.'/../Entity']
        );
        $config->setMetadataDriverImpl($driver);
        $config->setMetadataCacheImpl($cache);

        $conn = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        $em = \Doctrine\ORM\EntityManager::create($conn, $config);

        return $em;
    }

    protected function generateSchema()
    {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();

        if (!empty($metadatas)) {
            $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $tool->dropSchema($metadatas);
            $tool->createSchema($metadatas);
        }
    }
}
