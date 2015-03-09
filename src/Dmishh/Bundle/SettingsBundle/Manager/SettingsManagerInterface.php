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

use Dmishh\Bundle\SettingsBundle\Entity\SettingOwner;

interface SettingsManagerInterface
{
    const SCOPE_ALL    = 'all';
    const SCOPE_GLOBAL = 'global';
    const SCOPE_USER   = 'user';

    /**
     * Returns setting value by its name.
     *
     * @param string            $name
     * @param SettingOwner|null $owner
     * @param mixed|null        $default value to return if the setting is not set
     *
     * @return mixed
     */
    public function get($name, SettingOwner $owner = null, $default = null);

    /**
     * Returns all settings as associative name-value array.
     *
     * @param SettingOwner|null $owner
     *
     * @return array
     */
    public function all(SettingOwner $owner = null);

    /**
     * Sets setting value by its name.
     *
     * @param string            $name
     * @param mixed             $value
     * @param SettingOwner|null $owner
     *
     * @return SettingsManagerInterface
     */
    public function set($name, $value, SettingOwner $owner = null);

    /**
     * Sets settings' values from associative name-value array.
     *
     * @param array             $settings
     * @param SettingOwner|null $owner
     *
     * @return SettingsManagerInterface
     */
    public function setMany(array $settings, SettingOwner $owner = null);

    /**
     * Clears setting value.
     *
     * @param string            $name
     * @param SettingOwner|null $owner
     *
     * @return SettingsManagerInterface
     */
    public function clear($name, SettingOwner $owner = null);
}
