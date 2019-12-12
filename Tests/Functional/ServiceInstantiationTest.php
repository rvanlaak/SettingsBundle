<?php

namespace Dmishh\SettingsBundle\Tests\Functional;

use Dmishh\SettingsBundle\DmishhSettingsBundle;
use Dmishh\SettingsBundle\Manager\SettingsManager;
use Dmishh\SettingsBundle\Manager\SettingsManagerInterface;
use Dmishh\SettingsBundle\Serializer\PhpSerializer;
use Dmishh\SettingsBundle\Serializer\SerializerInterface;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;

/**
 * @internal
 */
final class ServiceInstantiationTest extends BaseBundleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Make services public that have an idea that matches a regex
        $this->addCompilerPass(new PublicServicePass('|Dmishh.*|'));
    }

    public function testInitBundle()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(\dirname(__DIR__).'/Resources/app/config/config.yml');
        $kernel->addBundle(DoctrineBundle::class);
        $this->bootKernel();
        $container = $this->getContainer();

        // Test if you services exists
        self::assertTrue($container->has(SerializerInterface::class));
        $service = $container->get(SerializerInterface::class);
        self::assertInstanceOf(PhpSerializer::class, $service);

        $service = $container->get(SettingsManagerInterface::class);
        self::assertInstanceOf(SettingsManager::class, $service);
    }

    protected function getBundleClass()
    {
        return DmishhSettingsBundle::class;
    }
}
