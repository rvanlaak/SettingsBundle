<?php

namespace Dmishh\SettingsBundle\Tests\Functional;

use Dmishh\SettingsBundle\DmishhSettingsBundle;
use Dmishh\SettingsBundle\Manager\SettingsManager;
use Dmishh\SettingsBundle\Manager\SettingsManagerInterface;
use Dmishh\SettingsBundle\Serializer\PhpSerializer;
use Dmishh\SettingsBundle\Serializer\SerializerInterface;
use Dmishh\SettingsBundle\Tests\CompilerPass\PublicServicePass;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @internal
 */
final class ServiceInstantiationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestCompilerPass(new PublicServicePass('|Dmishh.*|'));
        $kernel->addTestConfig(\dirname(__DIR__).'/Resources/app/config/config.yml');
        $kernel->addTestBundle(DoctrineBundle::class);
        $kernel->addTestBundle(DmishhSettingsBundle::class);
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testInitBundle(): void
    {
        $kernel = self::bootKernel();

        // Get the container
        $container = $kernel->getContainer();

        self::assertTrue($container->has(SerializerInterface::class), 'Serializer interface not found');
        $service = $container->get(SerializerInterface::class);
        self::assertInstanceOf(PhpSerializer::class, $service, 'PHP Serializer not found');

        $service = $container->get(SettingsManagerInterface::class);
        self::assertInstanceOf(SettingsManager::class, $service);
    }

    protected function getBundleClass(): string
    {
        return DmishhSettingsBundle::class;
    }
}
