<?php

namespace Dmishh\SettingsBundle\Tests\Functional;

use Dmishh\SettingsBundle\Serializer\PhpSerializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceInstantiationTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        static::bootKernel();
    }

    public function testHttpClient()
    {
        $container = static::$kernel->getContainer();

        $this->assertTrue($container->has('dmishh.settings.serializer'));
        $serializer = $container->get('dmishh.settings.serializer');
        $this->assertInstanceOf(PhpSerializer::class, $serializer);
    }
}