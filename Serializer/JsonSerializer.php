<?php

namespace Dmishh\SettingsBundle\Serializer;

use Dmishh\SettingsBundle\Exception\InvalidArgumentException;

class JsonSerializer implements SerializerInterface
{
    public function serialize(mixed $data): string
    {
        $serialized = json_encode($data);

        if (false === $serialized) {
            throw new InvalidArgumentException('Invalid argument: this argument cannot be serialized with this serializer');
        }

        return $serialized;
    }

    public function unserialize(string $serialized): mixed
    {
        return json_decode($serialized, true);
    }
}
