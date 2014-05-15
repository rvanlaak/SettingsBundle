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
