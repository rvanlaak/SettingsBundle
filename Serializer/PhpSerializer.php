<?php

namespace Dmishh\SettingsBundle\Serializer;

class PhpSerializer implements SerializerInterface
{
    public function serialize($data)
    {
        return serialize($data);
    }

    public function unserialize($serialized)
    {
        return unserialize($serialized);
    }
}
