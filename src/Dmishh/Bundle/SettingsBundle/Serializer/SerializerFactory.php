<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Serializer;

use Dmishh\Bundle\SettingsBundle\Exception\UnknownSerializerException;
use Symfony\Component\DependencyInjection\Container;

class SerializerFactory
{
    /**
     * @param string $name short name of serializer (ex.: php) or full class name
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\UnknownSerializerException
     * @return SerializerInterface
     */
    public static function create($name)
    {
        $serializerClass = 'Dmishh\\Bundle\\SettingsBundle\\Serializer\\' . Container::camelize($name) . 'Serializer';

        if (class_exists($serializerClass)) {
            return new $serializerClass;
        } else {
            $serializerClass = $name;

            if (class_exists($serializerClass)) {
                $serializer = new $serializerClass;
                if ($serializer instanceof SerializerInterface) {
                    return $serializer;
                }
            }
        }

        throw new UnknownSerializerException($serializerClass);
    }
}
