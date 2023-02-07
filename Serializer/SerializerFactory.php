<?php

namespace Dmishh\SettingsBundle\Serializer;

use Dmishh\SettingsBundle\Exception\UnknownSerializerException;
use Symfony\Component\DependencyInjection\Container;

class SerializerFactory
{
    /**
     * @param string $name short name of serializer (ex.: php) or full class name
     *
     * @throws UnknownSerializerException
     */
    public static function create(string $name): SerializerInterface
    {
        $serializerClass = 'Dmishh\\SettingsBundle\\Serializer\\'.Container::camelize($name).'Serializer';

        if (class_exists($serializerClass)) {
            $serializer = new $serializerClass();
            if ($serializer instanceof SerializerInterface) {
                return $serializer;
            }

            throw new UnknownSerializerException($serializerClass);
        }

        $serializerClass = $name;

        if (class_exists($serializerClass)) {
            $serializer = new $serializerClass();
            if ($serializer instanceof SerializerInterface) {
                return $serializer;
            }
        }

        throw new UnknownSerializerException($serializerClass);
    }
}
