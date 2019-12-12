<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
