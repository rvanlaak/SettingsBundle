<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Exception;

class UnknownSerializationTypeException extends SettingsException
{
    public function __construct($serialization)
    {
        parent::__construct(sprintf('Unknown serialization type "%s"', $serialization));
    }
}
