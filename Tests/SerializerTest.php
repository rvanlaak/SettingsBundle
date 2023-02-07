<?php

namespace Dmishh\SettingsBundle\Tests;

use Dmishh\SettingsBundle\Exception\UnknownSerializerException;
use Dmishh\SettingsBundle\Serializer\SerializerFactory;
use Dmishh\SettingsBundle\Tests\Serializer\CustomSerializer;

class SerializerTest extends AbstractTest
{
    public static array $testData = ['abc' => '123', 123, 5.0];

    public function testPhpSerializer(): void
    {
        $serializer = SerializerFactory::create('php');
        $this->assertEquals(serialize(null), $serializer->serialize(null));
        $this->assertEquals(serialize(self::$testData), $serializer->serialize(self::$testData));
        $this->assertEquals(self::$testData, $serializer->unserialize($serializer->serialize(self::$testData)));
    }

    public function testJsonSerializer(): void
    {
        $serializer = SerializerFactory::create('json');
        $this->assertEquals(json_encode(null, \JSON_THROW_ON_ERROR), $serializer->serialize(null));
        $this->assertEquals(json_encode(self::$testData, \JSON_THROW_ON_ERROR), $serializer->serialize(self::$testData));
        $this->assertEquals(self::$testData, $serializer->unserialize($serializer->serialize(self::$testData)));
    }

    public function testCustomSerializer(): void
    {
        $serializer = SerializerFactory::create(CustomSerializer::class);
        $this->assertNull($serializer->unserialize($serializer->serialize(null)));
        $this->assertEquals(self::$testData, $serializer->unserialize($serializer->serialize(self::$testData)));
    }

    public function testUnknownSerializer(): void
    {
        $this->expectException(UnknownSerializerException::class);
        SerializerFactory::create('unknown_serializer');
    }
}
