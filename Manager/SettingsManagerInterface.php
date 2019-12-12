<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\SettingsBundle\Manager;

use Dmishh\SettingsBundle\Entity\SettingsOwnerInterface;

interface SettingsManagerInterface
{
    const SCOPE_ALL = 'all';
    const SCOPE_GLOBAL = 'global';
    const SCOPE_USER = 'user';

    /**
     * Returns setting value by its name.
     *
     * @param mixed|null $default value to return if the setting is not set
     */
    public function get(string $name, ?SettingsOwnerInterface $owner = null, $default = null);

    /**
     * Returns all settings as associative name-value array.
     */
    public function all(?SettingsOwnerInterface $owner = null): array;

    /**
     * Sets setting value by its name.
     */
    public function set(string $name, $value, ?SettingsOwnerInterface $owner = null): void;

    /**
     * Sets settings' values from associative name-value array.
     */
    public function setMany(array $settings, ?SettingsOwnerInterface $owner = null): void;

    /**
     * Clears setting value.
     */
    public function clear(string $name, SettingsOwnerInterface $owner = null): void;
}
