<?php

namespace Dmishh\SettingsBundle\Serializer;

use Dmishh\SettingsBundle\Exception\InvalidArgumentException;

class JsonSerializer implements SerializerInterface
{
    /**
     * @throws \JsonException
     */
    public function serialize(mixed $data): string
    {
        $serialized = json_encode($data, \JSON_THROW_ON_ERROR);

        /* @phpstan-ignore-next-line */
        if (false === $serialized) {
            throw new InvalidArgumentException('Invalid argument: this argument cannot be serialized with this serializer');
        }

        return $serialized;
    }

    /**
     * @throws \JsonException
     */
    public function unserialize(string $serialized): mixed
    {
        return json_decode($serialized, true);
    }
}
