<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Entity;

/**
 * This interface must be implemented by the Entity connected to a setting
 */
interface SettingsOwnerInterface
{
    public function getSettingIdentifier();
}
