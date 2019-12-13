<?php

namespace Dmishh\SettingsBundle\Tests\Serializer;

use Dmishh\SettingsBundle\Serializer\SerializerInterface;

class CustomSerializer implements SerializerInterface
{
    public function serialize($data)
    {
        return serialize(json_encode($data));
    }

    public function unserialize($serialized)
    {
        return json_decode(unserialize($serialized), true);
    }
}
