<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Manager;

use Dmishh\Bundle\SettingsBundle\Entity\UserInterface;

interface SettingsManagerInterface
{
    function get($name, UserInterface $user = null);
    function set($name, $value, UserInterface $user = null);
    function clear($name, UserInterface $user = null);
    function all(UserInterface $user = null);
}
