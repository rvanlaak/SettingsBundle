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
    const SCOPE_ALL = 'all';
    const SCOPE_GLOBAL = 'global';
    const SCOPE_USER = 'user';

    /**
     * @param string $name
     * @param UserInterface $user
     * @return mixed
     */
    function get($name, UserInterface $user = null);

    /**
     * @param UserInterface $user
     * @return array
     */
    function all(UserInterface $user = null);

    /**
     * @param string $name
     * @param mixed $value
     * @param UserInterface $user
     * @return SettingsManagerInterface
     */
    function set($name, $value, UserInterface $user = null);

    /**
     * @param array $settings
     * @param UserInterface $user
     * @return SettingsManagerInterface
     */
    function setMany(array $settings, UserInterface $user = null);

    /**
     * @param string $name
     * @param UserInterface $user
     * @return SettingsManagerInterface
     */
    function clear($name, UserInterface $user = null);
}
