<?php

namespace Dmishh\SettingsBundle\Serializer;

use Dmishh\SettingsBundle\Exception\UnknownSerializerException;
use Symfony\Component\DependencyInjection\Container;

class SerializerFactory
{
    /**
     * @param string $name short name of serializer (ex.: php) or full class name
     *
     * @throws \Dmishh\SettingsBundle\Exception\UnknownSerializerException
     *
     * @return SerializerInterface
     */
    public static function create($name)
    {
        $serializerClass = 'Dmishh\\SettingsBundle\\Serializer\\'.Container::camelize($name).'Serializer';

        if (class_exists($serializerClass)) {
            return new $serializerClass();
        } else {
            $serializerClass = $name;

            if (class_exists($serializerClass)) {
                $serializer = new $serializerClass();
                if ($serializer instanceof SerializerInterface) {
                    return $serializer;
                }
            }
        }

        throw new UnknownSerializerException($serializerClass);
    }
}
