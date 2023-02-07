<?php

namespace Dmishh\SettingsBundle\Serializer;

interface SerializerInterface
{
    public function serialize(mixed $data): string;

    public function unserialize(string $serialized): mixed;
}
