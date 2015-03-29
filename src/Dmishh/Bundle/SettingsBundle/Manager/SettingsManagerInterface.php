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

use Dmishh\Bundle\SettingsBundle\Entity\SettingsOwnerInterface;

interface SettingsManagerInterface
{
    const SCOPE_ALL    = 'all';
    const SCOPE_GLOBAL = 'global';
    const SCOPE_USER   = 'user';

    /**
     * Returns setting value by its name.



*
*@param string            $name
     * @param SettingsOwnerInterface|null $owner
     * @param mixed|null        $default value to return if the setting is not set



*
*@return mixed
     */
    public function get($name, SettingsOwnerInterface $owner = null, $default = null);

    /**
     * Returns all settings as associative name-value array.



*
*@param SettingsOwnerInterface|null $owner



*
*@return array
     */
    public function all(SettingsOwnerInterface $owner = null);

    /**
     * Sets setting value by its name.



*
*@param string            $name
     * @param mixed             $value
     * @param SettingsOwnerInterface|null $owner



*
*@return SettingsManagerInterface
     */
    public function set($name, $value, SettingsOwnerInterface $owner = null);

    /**
     * Sets settings' values from associative name-value array.



*
*@param array             $settings
     * @param SettingsOwnerInterface|null $owner



*
*@return SettingsManagerInterface
     */
    public function setMany(array $settings, SettingsOwnerInterface $owner = null);

    /**
     * Clears setting value.



*
*@param string            $name
     * @param SettingsOwnerInterface|null $owner



*
*@return SettingsManagerInterface
     */
    public function clear($name, SettingsOwnerInterface $owner = null);
}
