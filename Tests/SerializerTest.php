<?php

namespace Dmishh\SettingsBundle\Tests;

use Dmishh\SettingsBundle\Serializer\SerializerFactory;

class SerializerTest extends AbstractTest
{
    public static $testData = ['abc' => '123', 123, 5.0];

    public function testPhpSerializer()
    {
        $serializer = SerializerFactory::create('php');
        $this->assertEquals(serialize(self::$testData), $serializer->serialize(self::$testData));
        $this->assertEquals(self::$testData, $serializer->unserialize($serializer->serialize(self::$testData)));
    }

    public function testJsonSerializer()
    {
        $serializer = SerializerFactory::create('json');
        $this->assertEquals(json_encode(self::$testData), $serializer->serialize(self::$testData));
        $this->assertEquals(self::$testData, $serializer->unserialize($serializer->serialize(self::$testData)));
    }

    public function testCustomSerializer()
    {
        $serializer = SerializerFactory::create('Dmishh\SettingsBundle\Tests\Serializer\CustomSerializer');
        $this->assertEquals(self::$testData, $serializer->unserialize($serializer->serialize(self::$testData)));
    }

    /**
     * @expectedException \Dmishh\SettingsBundle\Exception\UnknownSerializerException
     */
    public function testUnknownSerializer()
    {
        $serializer = SerializerFactory::create('unknown_serializer');
    }
}
