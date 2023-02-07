<?php

namespace Dmishh\SettingsBundle\Serializer;

class PhpSerializer implements SerializerInterface
{
    public function serialize(mixed $data): string
    {
        return serialize($data);
    }

    public function unserialize(string $serialized): mixed
    {
        return unserialize($serialized);
    }
}
