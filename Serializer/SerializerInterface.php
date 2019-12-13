<?php

namespace Dmishh\SettingsBundle\Serializer;

interface SerializerInterface
{
    /**
     * @return string
     */
    public function serialize($data);

    /**
     * @param string $serialized
     */
    public function unserialize($serialized);
}
