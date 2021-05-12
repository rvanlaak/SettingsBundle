<?php

namespace Dmishh\SettingsBundle\Serializer;

class JsonSerializer implements SerializerInterface
{
    public function serialize($data)
    {
        return json_encode($data);
    }

    public function unserialize($serialized)
    {
        return json_decode($serialized, true);
    }
}
