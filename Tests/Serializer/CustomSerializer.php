<?php

namespace Dmishh\SettingsBundle\Tests\Serializer;

use Dmishh\SettingsBundle\Serializer\SerializerInterface;

class CustomSerializer implements SerializerInterface
{
    public function serialize(mixed $data): string
    {
        return serialize(json_encode($data, \JSON_THROW_ON_ERROR));
    }

    public function unserialize(string $serialized): mixed
    {
        return json_decode(unserialize($serialized), true, 512, \JSON_THROW_ON_ERROR);
    }
}
